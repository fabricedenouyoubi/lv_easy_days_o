<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;
use Modules\RhFeuilleDeTempsReguliere\Workflows\FeuilleTempsWorkflow;
use Workflow\WorkflowStub;
use Carbon\Carbon;

class RhFeuilleDeTempsReguliereList extends Component
{
    use WithPagination;

    public $employe;
    public $anneeFinanciere;
    public $feuillesActives = [];
    public $banqueTemps = [];

    public $informationsEmploye = [];

    // Banque de temps
    public $banqueDeTemps = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        try {
            // Récupérer l'employé connecté
            $this->employe = Auth::user()->employe;

            // Récupérer l'année financière active
            $this->anneeFinanciere = AnneeFinanciere::where('actif', true)->first();

            // Calculer les informations supplémentaires
            $this->calculerInformationsEmploye();

            // Calculer la banque de temps
            $this->calculerBanqueDeTemps();
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
            ->with(['operations' => function ($query) {
                $query->where('employe_id', $this->employe->id);
            }])
            ->paginate(10);

        // Enrichir avec les données d'opération
        $semaines->getCollection()->transform(function ($semaine) {
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
     * Créer une nouvelle feuille de temps (opération) avec workflow
     */
    public function creerFeuilleTemps($semaineId)
    {
        $user_connect = Auth::user();
        
        try {
            // Données pour créer l'opération
            $insertData = [
                'employe_id' => $this->employe->id,
                'annee_semaine_id' => $semaineId,
                'workflow_state' => 'brouillon',
                'statut' => 'Brouillon',
                'total_heure' => 0,
                'total_heure_regulier' => 0,
                'total_heure_supp' => 0,
            ];

            $comment = 'Feuille de temps créée par ' . $user_connect->name;

            // Vérifier si l'opération existe déjà
            $operationExistante = Operation::where('employe_id', $this->employe->id)
                ->where('annee_semaine_id', $semaineId)
                ->first();

            if ($operationExistante) {
                // Rediriger vers l'opération existante
                return redirect()->route('feuille-temps.edit', [
                    'semaineId' => $semaineId,
                    'operationId' => $operationExistante->id
                ]);
            }

            //--- Workflow ---
            $workflow = WorkflowStub::make(FeuilleTempsWorkflow::class);
            $operation = null; // Nouvelle opération
            $workflow->start($operation, 'enregistrer', ['comment' => $comment], $insertData);

            while ($workflow->running());

            if ($workflow->failed()) {
                Log::channel('daily')->error(
                    "Erreur lors du lancement du workflow de création de feuille de temps pour l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                    ['semaine' => $semaineId]
                );
                session()->flash('error', 'Une erreur est survenue lors du lancement du workflow de création.');
                return;
            }

            // Récupérer l'opération créée
            $nouvelleOperation = Operation::where('employe_id', $this->employe->id)
                ->where('annee_semaine_id', $semaineId)
                ->latest()
                ->first();

            if ($nouvelleOperation) {
                Log::channel('daily')->info("Feuille de temps créée pour l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name, ['operation' => $nouvelleOperation->id]);

                // Rediriger vers le formulaire d'édition
                return redirect()->route('feuille-temps.edit', [
                    'semaineId' => $semaineId,
                    'operationId' => $nouvelleOperation->id
                ]);
            } else {
                session()->flash('error', 'Erreur lors de la création de la feuille de temps.');
            }

        } catch (\Throwable $th) {
            Log::channel('daily')->error(
                "Erreur lors de la création de feuille de temps pour l'employé " . $this->employe->nom . " " . $this->employe->prenom . " par l'utilisateur " . $user_connect->name,
                ['message' => $th->getMessage(), 'semaine' => $semaineId]
            );
            session()->flash('error', 'Erreur lors de la création: ' . $th->getMessage());
        }
    }

    /**
     * Mapping des couleurs entre les classes CSS et les types de boutons
     */
    private function getColorMapping()
    {
        return [
            'bg-warning' => 'warning',
            'bg-info' => 'info',
            'bg-primary' => 'primary',
            'bg-success' => 'success',
            'bg-danger' => 'danger',
            'bg-secondary' => 'secondary'
        ];
    }

    /**
     * Extraire le type de couleur depuis une classe CSS
     */
    private function getTypeFromClass($class)
    {
        $mapping = $this->getColorMapping();

        // Extraire la classe principale (bg-warning, bg-info, etc.)
        $mainClass = explode(' ', $class)[0];

        return $mapping[$mainClass] ?? 'secondary';
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

        return match ($operation['workflow_state']) {
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
            // Récupérer le statut formaté pour obtenir la couleur cohérente
            $statutInfo = $this->getStatutFormate($semaine);
            $colorType = $this->getTypeFromClass($statutInfo['class']);

            // Opération existe = actions selon l'état du workflow
            switch ($operation['workflow_state']) {
                case 'brouillon':
                case 'en_cours':
                case 'rejete':
                    $actions[] = [
                        'type' => $colorType,
                        'text' => 'Modifier',
                        'icon' => 'fas fa-edit',
                        'route' => 'feuille-temps.edit',
                        'params' => ['semaineId' => $semaine->id, 'operationId' => $operation['id']]
                    ];
                    break;

                default:
                    $actions[] = [
                        'type' => $colorType,
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

    // Formaté l'affichage de la date
    public function getPeriodeFormatteeAttribute()
    {
        $debut = Carbon::parse($this->date_debut)->locale('fr')->format('F Y');
        $fin = Carbon::parse($this->date_fin)->locale('fr')->format('F Y');

        return $debut . ' - ' . $fin;
    }

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-list', [
            'feuilles_temps' => $this->getFeuillesDeTemps()
        ]);
    }
}