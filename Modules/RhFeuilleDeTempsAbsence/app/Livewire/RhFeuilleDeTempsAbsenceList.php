<?php

namespace Modules\RhFeuilleDeTempsAbsence\Livewire;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsAbsence\Traits\AbsenceResource;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class RhFeuilleDeTempsAbsenceList extends Component
{
    use WithPagination, AbsenceResource;

    public $showAddAbsenceModal = false;
    public $demandeAbsenceId = null;
    public $nbrDemandeEnAttente;
    public $nbrDemandeApprouve;

    public $banqueTemps = [];
    public $employe;
    // Banque de temps
    public $banqueDeTemps = [];


    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'demandeAbsenceAjoute' => 'demandeAbsenceAjoute',
        'demandeAbsenceAjouteWorkflowError' => 'demandeAbsenceAjouteWorkflowError'
    ];


    public function mount()
    {
        if (Auth::user()->employe->est_gestionnaire) {
            //--- Nombre d'absences  approuvée et en attente affichées si un gestionnaire est connecté
            //--- en attente
            $this->nbrDemandeEnAttente = DemandeAbsence::gestionnaireConnecte()
                ->EnAttente()->count();

            //--- approuvée
            $this->nbrDemandeApprouve = DemandeAbsence::gestionnaireConnecte()
                ->approuve()->count();
        } else {
            //--- Nombre d'absences  approuvée et en attente affichées si un employé est connecté
            //--- en attente
            $this->nbrDemandeEnAttente = DemandeAbsence::employeConnecte()
                ->EnAttente()->count();

            //--- approuvée
            $this->nbrDemandeApprouve = DemandeAbsence::employeConnecte()
                ->approuve()->count();
        }

        // Récupérer l'employé connecté
        $this->employe = Auth::user()->employe;

        // Calculer la banque de temps
        $this->calculerBanqueDeTemps();
    }

    //--- afficher et caher le formulaire d'ajout d'une absence
    public function toogle_add_absence_modal()
    {
        $this->showAddAbsenceModal = !$this->showAddAbsenceModal;
    }

    //--- afficher le message de creation d'une absence
    public function demandeAbsenceAjoute()
    {
        $this->showAddAbsenceModal = false;
        session()->flash('success', 'Demande d\'absence enregistrée avec succès.');
    }

    public function demandeAbsenceAjouteWorkflowError()
    {
        $this->showAddAbsenceModal = false;
        session()->flash('error', 'Erreur lors du lancement du workflow de creation de la  demande d\'absence');
    }

    /**
     * Calculer la banque de temps dynamique (version complète avec collectif)
     */
    private function calculerBanqueDeTemps()
    {
        $banqueTemps = [];

        // Récupérer l'année financière active
        $anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

        if (!$anneeFinanciere || !$this->employe) {
            $this->banqueDeTemps = [];
            return;
        }

        // 1. RECHERCHE INDIVIDUELLE : Configurations spécifiques à cet employé
        $configurationsIndividuelles = Configuration::with('codeTravail')
            ->where('employe_id', $this->employe->id)
            ->where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereHas('codeTravail', function ($query) {
                $query->where('est_banque', true);
            })
            ->get();

        // Créer un tableau des codes déjà trouvés en individuel
        $codesIndividuels = $configurationsIndividuelles->pluck('code_travail_id')->toArray();

        // 2. RECHERCHE COLLECTIVE : Configurations collectives (employe_id = NULL)
        $configurationsCollectives = Configuration::with(['codeTravail', 'employes'])
            ->whereNull('employe_id')
            ->whereNull('date')
            ->where('annee_budgetaire_id', $anneeFinanciere->id)
            ->whereHas('codeTravail', function ($query) {
                $query->where('est_banque', true);
            })
            ->whereHas('employes', function ($query) {
                $query->where('employe_id', $this->employe->id);
            })
            ->whereNotIn('code_travail_id', $codesIndividuels) // Exclure ceux déjà trouvés en individuel
            ->get();

        // 3. TRAITEMENT DES CONFIGURATIONS INDIVIDUELLES
        foreach ($configurationsIndividuelles as $config) {
            $codeTravail = $config->codeTravail;

            if ($codeTravail->cumule_banque) {
                $valeur = $config->quota ?? 0;
            } else {
                $valeur = $config->reste ?? 0;
            }

            $banqueTemps[] = [
                'type' => $codeTravail->code,
                'libelle' => $codeTravail->libelle,
                'valeur' => $valeur,
                'code_travail' => $codeTravail,
                'configuration' => $config,
                'utilise_quota' => $codeTravail->cumule_banque,
                'est_collectif' => false // Pour debug
            ];
        }

        // 4. TRAITEMENT DES CONFIGURATIONS COLLECTIVES
        foreach ($configurationsCollectives as $config) {
            $codeTravail = $config->codeTravail;

            if ($codeTravail->cumule_banque) {
                $valeur = $config->quota ?? 0;
            } else {
                $valeur = $config->reste ?? 0;
            }

            $banqueTemps[] = [
                'type' => $codeTravail->code,
                'libelle' => $codeTravail->libelle,
                'valeur' => $valeur,
                'code_travail' => $codeTravail,
                'configuration' => $config,
                'utilise_quota' => $codeTravail->cumule_banque,
                'est_collectif' => true // Pour debug
            ];
        }

        // Trier par libellé pour un affichage cohérent
        usort($banqueTemps, function ($a, $b) {
            return strcmp($a['libelle'], $b['libelle']);
        });

        $this->banqueDeTemps = $banqueTemps;
    }

    /**
     * Calculer le total de la banque de temps
     */
    public function getTotalBanqueTempsProperty()
    {
        return collect($this->banqueDeTemps)->sum('valeur');
    }

    //--- recuperation des demandes d'absence en cours
    public function getDemandeAbsence()
    {

        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
                ->gestionnaireConnecte()
                ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
                ->paginate(10);
        }

        return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
            ->employeConnecte()
            ->whereDate('date_fin', '>=', \Carbon\Carbon::today())
            ->paginate(10);
    }

    //--- recuperation des demandes d'absence cloturées
    public function getDemandeAbsenceClose()
    {
        //--- absences affichées si un gestionnaire est connecté
        if (Auth::user()->employe->est_gestionnaire) {
            return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
                ->gestionnaireConnecte()
                ->whereDate('date_fin', '<', \Carbon\Carbon::today())
                ->paginate(10);
        }

        //--- absences affichées si un employé est connecté
        return DemandeAbsence::with(['employe', 'codeTravail', 'operations.anneeSemaine'])
            ->employeConnecte()
            ->whereDate('date_fin', '<', \Carbon\Carbon::today())
            ->paginate(10);
    }

    public function render()
    {
        return view(
            'rhfeuilledetempsabsence::livewire.rh-feuille-de-temps-absence-list',
            [
                'demande_absences' => $this->getDemandeAbsence(),
                'demande_absences_close' => $this->getDemandeAbsenceClose()

            ]
        );
    }
}
