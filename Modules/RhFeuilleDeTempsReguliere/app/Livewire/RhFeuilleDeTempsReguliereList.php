<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class RhFeuilleDeTempsReguliereList extends Component
{
    use WithPagination;

    public $employe;
    public $anneeFinanciere;
    public $feuillesActives = [];
    public $banqueTemps = [];

    public $informationsEmploye = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        try {
            // Récupérer l'employé connecté
            $this->employe = Auth::user()->employe;

            // Récupérer l'année financière active
            $this->anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

            // Calculer la banque de temps
            $this->calculerBanqueTemps();

            // Calculer les informations supplémentaires
            $this->calculerInformationsEmploye();

        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du chargement des données: ' . $th->getMessage());
        }
    }

    /**
     * Récupérer les feuilles de temps actives pour l'employé
     */
    public function getFeuillesDeTemps()
    {
        if (!$this->employe || !$this->anneeFinanciere) {
            return collect();
        }

        // Récupérer uniquement les semaines actives
        $semaines = SemaineAnnee::where('annee_financiere_id', $this->anneeFinanciere->id)
            ->where('actif', true)
            ->orderBy('numero_semaine')
            ->with(['operations' => function($query) {
                $query->where('employe_id', $this->employe->id);
            }])
            ->paginate(10);

        // Enrichir avec les données d'opération
        $semaines->getCollection()->transform(function($semaine) {
            $operation = $semaine->operations->first();

            $semaine->operation_data = [
                'id' => $operation?->id,
                'workflow_state' => $operation?->workflow_state ?? null,
                'statut' => $operation?->statut ?? null,
                'total_heure' => $operation?->total_heure ?? 0,
                'created_at' => $operation?->created_at,
                'updated_at' => $operation?->updated_at,
            ];

            return $semaine;
        });

        return $semaines;
    }

    /**
     * Créer une nouvelle feuille de temps (opération)
     */
    public function creerFeuilleTemps($semaineId)
    {
        try {
            // Créer l'opération avec état brouillon
            $operation = Operation::getOrCreateOperation($this->employe->id, $semaineId);

            // Rediriger vers le formulaire d'édition
            return redirect()->route('feuille-temps.edit', [
                'semaineId' => $semaineId,
                'operationId' => $operation->id
            ]);

        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la création: ' . $th->getMessage());
        }
    }

    /**
     * Calculer la banque de temps de l'employé
     */
    private function calculerBanqueTemps()
    {
        // Logique basique - à enrichir selon vos règles métier
        $this->banqueTemps = [
            'vacances' => 5, // heures
            'banque_temps' => 10,
            'heure_csn' => 45,
            'total_heures_banque' => 60
        ];
    }

    /**
     * Obtenir le statut formaté pour l'affichage
     */
    public function getStatutFormate($semaine)
    {
        $operation = $semaine->operation_data;

        if (!$operation['id']) {
            return [
                'text' => '---',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-minus-circle'
            ];
        }

        return match($operation['workflow_state']) {
            'brouillon' => [
                'text' => 'Brouillon',
                'class' => 'bg-warning text-dark',
                'icon' => 'fas fa-pencil-alt'
            ],
            'en_cours' => [
                'text' => 'En cours',
                'class' => 'bg-info text-dark',
                'icon' => 'fas fa-hourglass-half'
            ],
            'soumis' => [
                'text' => 'Soumis',
                'class' => 'bg-primary',
                'icon' => 'fas fa-inbox'
            ],
            'valide' => [
                'text' => 'Validé',
                'class' => 'bg-success',
                'icon' => 'fas fa-check-circle'
            ],
            'rejete' => [
                'text' => 'Rejeté',
                'class' => 'bg-danger',
                'icon' => 'fas fa-times-circle'
            ],
            default => [
                'text' => 'Inconnu',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-question-circle'
            ]
        };
    }

    /**
     * Obtenir les actions disponibles pour une semaine
     */
    public function getActionsDisponibles($semaine)
    {
        $operation = $semaine->operation_data;
        $actions = [];

        if (!$operation['id']) {
            // Aucune opération = bouton créer
            $actions[] = [
                'type' => 'success',
                'text' => 'Créer',
                'icon' => 'fas fa-plus-circle',
                'action' => 'creerFeuilleTemps',
                'params' => [$semaine->id]
            ];
        } else {
            // Opération existe = actions selon l'état
            switch($operation['workflow_state']) {
                case 'brouillon':
                case 'en_cours':
                case 'rejete':  // Ajout de l'état rejeté pour permettre la modification
                    $actions[] = [
                        'type' => $operation['workflow_state'] === 'rejete' ? 'warning' : 'warning',
                        'text' => 'Consulter',
                        'icon' => 'fas fa-edit',
                        'route' => 'feuille-temps.show',
                        'params' => ['semaineId' => $semaine->id, 'operationId' => $operation['id']]
                    ];
                    break;

                default:
                    $actions[] = [
                        'type' => 'info',
                        'text' => 'Consulter',
                        'icon' => 'fas fa-eye',
                        'route' => 'feuille-temps.show',
                        'params' => ['semaineId' => $semaine->id, 'operationId' => $operation['id']]
                    ];
            }
        }

        return $actions;
    }

    /**
 * Calculer les informations supplémentaires de l'employé
 */
private function calculerInformationsEmploye()
{
    $this->informationsEmploye = [
        'anniversaire' => $this->employe->date_de_naissance,
        'prochain_jour_ferie' => $this->getProchainJourFerie(),
        'semaine_normale' => '35h' 
    ];
}

/**
 * Obtenir le prochain jour férié
 */
private function getProchainJourFerie()
{
    if (!$this->employe || !$this->anneeFinanciere) {
        return null;
    }

    // Rechercher les codes de travail contenant "férié"
    $codesFeries = CodeTravail::where('libelle', 'LIKE', '%férié%')
        ->orWhere('libelle', 'LIKE', '%ferie%')
        ->orWhere('code', 'LIKE', '%FERIE%')
        ->pluck('id');

    if ($codesFeries->isEmpty()) {
        return null;
    }

    // Rechercher la prochaine date de jour férié dans les configurations
    $prochainJourFerie = Configuration::whereIn('code_travail_id', $codesFeries)
        ->where('annee_budgetaire_id', $this->anneeFinanciere->id)
        ->where('date', '>=', now()->toDateString())
        ->orderBy('date')
        ->first();

    return $prochainJourFerie;
}

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-list', [
            'feuilles_temps' => $this->getFeuillesDeTemps()
        ]);
    }
}
