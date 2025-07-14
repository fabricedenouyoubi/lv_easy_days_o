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
     * Calculer la banque de temps dynamique
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

        // Définir les codes à rechercher pour la banque de temps
        $codesRecherches = [
            'vacances' => ['VAC', 'VACATION', 'VACANCE', 'CONGE'],
            'banque_temps' => ['CAISS', 'BANQUE', 'BANK', 'BT'],
            'heure_csn' => ['CSN', 'HCSN', 'CSN_H']
        ];

        foreach ($codesRecherches as $type => $patterns) {
            $configuration = $this->rechercherConfigurationParCode($patterns, $anneeFinanciere->id);

            if ($configuration) {
                $banqueTemps[] = [
                    'type' => $type,
                    'libelle' => $this->getLibelleBanqueTemps($type, $configuration->codeTravail->libelle),
                    'valeur' => $configuration->reste ?? 0,
                    'code_travail' => $configuration->codeTravail
                ];
            }
        }

        $this->banqueDeTemps = $banqueTemps;
    }


    /**
     * Version corrigée - reste collectif partagé
     */
    private function rechercherConfigurationParCode($patterns, $anneeBudgetaireId)
    {
        // Recherche individuelle
        $configIndividuelle = Configuration::with('codeTravail')
            ->where('employe_id', $this->employe->id)
            ->where('annee_budgetaire_id', $anneeBudgetaireId)
            ->whereHas('codeTravail', function ($query) use ($patterns) {
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) {
                        $subQuery->orWhere('code', 'LIKE', "%{$pattern}%")
                            ->orWhere('libelle', 'LIKE', "%{$pattern}%");
                    }
                });
            })
            ->first();

        if ($configIndividuelle) {
            return $configIndividuelle;
        }

        // Recherche collective avec reste partagé
        $configCollective = Configuration::with(['codeTravail', 'employes'])
            ->whereNull('employe_id')
            ->whereNull('date')
            ->where('annee_budgetaire_id', $anneeBudgetaireId)
            ->whereHas('codeTravail', function ($query) use ($patterns) {
                $query->where(function ($subQuery) use ($patterns) {
                    foreach ($patterns as $pattern) {
                        $subQuery->orWhere('code', 'LIKE', "%{$pattern}%")
                            ->orWhere('libelle', 'LIKE', "%{$pattern}%");
                    }
                });
            })
            ->whereHas('employes', function ($query) {
                $query->where('employe_id', $this->employe->id);
            })
            ->first();

        if ($configCollective) {
            // Retourner la configuration avec son reste
            return $configCollective;
        }

        return null;
    }

    /**
     * Obtenir le libellé formaté pour la banque de temps
     */
    private function getLibelleBanqueTemps($type, $libelleBrut)
    {
        return match ($type) {
            'vacances' => 'Vacances',
            'banque_temps' => 'Banque de temps',
            'heure_csn' => 'Heure CSN',
            default => $libelleBrut
        };
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
