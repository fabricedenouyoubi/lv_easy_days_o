<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class RhFeuilleDeTempsReguliereEdit extends Component
{
    public $operationId;
    public $semaineId;
    public $operation;
    public $semaine;
    public $employe;

    // Données des lignes de travail
    public $lignesTravail = [];
    public $codesTravauxDisponibles = [];

    // Totaux calculés
    public $totaux = [
        'total_heure' => 0,
        'total_heure_regulier' => 0,
        'total_heure_formation' => 0,
        'total_heure_deplacement' => 0,
        'total_heure_supp' => 0,
        'total_heure_csn' => 0,
        'total_heure_caisse' => 0,
        'total_heure_conge_mobile' => 0,
    ];

    // Jours de la semaine avec dates complètes
    public $datesSemaine = [];
    public $joursFeries = [];

    protected $rules = [
        'lignesTravail.*.duree_0' => 'nullable|string|max:5',
        'lignesTravail.*.duree_1' => 'nullable|string|max:5',
        'lignesTravail.*.duree_2' => 'nullable|string|max:5',
        'lignesTravail.*.duree_3' => 'nullable|string|max:5',
        'lignesTravail.*.duree_4' => 'nullable|string|max:5',
        'lignesTravail.*.duree_5' => 'nullable|string|max:5',
        'lignesTravail.*.duree_6' => 'nullable|string|max:5',
    ];

    public function mount()
    {
        try {
            $this->employe = Auth::user()->employe;
            $this->semaine = SemaineAnnee::findOrFail($this->semaineId);
            $this->operation = Operation::with(['lignesTravail.codeTravail'])->findOrFail($this->operationId);

            // Vérifier les permissions
            if ($this->operation->employe_id !== $this->employe->id) {
                abort(403, 'Accès non autorisé');
            }

            // Charger les codes de travail disponibles
            $this->chargerCodesTravauxDisponibles();

            // Calculer les dates de la semaine
            $this->calculerDatesSemaine();

            // Générer les lignes de travail
            $this->genererLignesTravail();

            // Calculer les jours fériés
            $this->calculerJoursFeries();

            // Calculer les totaux
            $this->calculerTotaux();

        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du chargement: ' . $th->getMessage());
        }
    }

    /**
     * Convertir une valeur décimale en format d'affichage (00.00)
     */
    private function formatDecimalToDisplay($value)
    {
        if (empty($value) || $value == 0) {
            return '00.00';
        }

        $floatValue = floatval($value);
        return sprintf('%05.2f', $floatValue);
    }

    /**
     * Convertir une valeur saisie par l'utilisateur en décimal
     */
    private function parseUserInputToDecimal($input)
    {
        if (empty($input) || trim($input) === '') {
            return 0.00;
        }

        // Remplacer les virgules par des points
        $input = str_replace(',', '.', trim($input));

        // Vérifier si c'est un nombre valide
        if (!is_numeric($input)) {
            return 0.00;
        }

        $value = floatval($input);

        // Limiter à 12 heures maximum et 2 décimales
        return min(12.00, round($value, 2));
    }

    /**
     * Charger les codes de travail disponibles
     */
    private function chargerCodesTravauxDisponibles()
    {
        // Récupérer TOUS les codes de travail (y compris les absences)
        $this->codesTravauxDisponibles = CodeTravail::with('categorie')
            ->orderBy('libelle')
            ->get();
    }

    /**
     * Calculer les dates de la semaine pour affichage
     */
    private function calculerDatesSemaine()
    {
        $dateDebut = \Carbon\Carbon::parse($this->semaine->debut);

        for ($i = 0; $i <= 6; $i++) {
            $date = $dateDebut->copy()->addDays($i);
            $this->datesSemaine[] = [
                'date' => $date,
                'format' => $date->format('d') . ' ' . $date->locale('fr')->monthName . ' ' . $date->format('Y'),
                'is_dimanche' => $date->isSunday()
            ];
        }
    }

    /**
     * Générer les lignes de travail basées sur les codes de travail
     */
    private function genererLignesTravail()
    {
        $lignesExistantes = $this->operation->lignesTravail->keyBy('codes_travail_id');

        // Créer une ligne pour chaque code de travail
        foreach ($this->codesTravauxDisponibles as $codeTravail) {
            $ligneExistante = $lignesExistantes->get($codeTravail->id);

            $this->lignesTravail[] = [
                'id' => $ligneExistante?->id,
                'codes_travail_id' => $codeTravail->id,
                'code_travail' => $codeTravail,
                'duree_0' => $this->formatDecimalToDisplay($ligneExistante?->duree_0 ?? 0),
                'duree_1' => $this->formatDecimalToDisplay($ligneExistante?->duree_1 ?? 0),
                'duree_2' => $this->formatDecimalToDisplay($ligneExistante?->duree_2 ?? 0),
                'duree_3' => $this->formatDecimalToDisplay($ligneExistante?->duree_3 ?? 0),
                'duree_4' => $this->formatDecimalToDisplay($ligneExistante?->duree_4 ?? 0),
                'duree_5' => $this->formatDecimalToDisplay($ligneExistante?->duree_5 ?? 0),
                'duree_6' => $this->formatDecimalToDisplay($ligneExistante?->duree_6 ?? 0),
                'auto_rempli' => $ligneExistante?->auto_rempli ?? false,
            ];
        }
    }

    /**
     * Calculer les totaux à partir des lignes
     */
    public function calculerTotaux()
    {
        $totaux = [
            'total_heure' => 0,
            'total_heure_regulier' => 0,
            'total_heure_formation' => 0,
            'total_heure_deplacement' => 0,
            'total_heure_supp' => 0,
            'total_heure_csn' => 0,
            'total_heure_caisse' => 0,
            'total_heure_conge_mobile' => 0,
        ];

        foreach ($this->lignesTravail as $ligne) {
            // Calculer le total des heures pour cette ligne
            $totalLigne = 0;
            for ($jour = 0; $jour <= 6; $jour++) {
                $duree = $this->parseUserInputToDecimal($ligne["duree_{$jour}"] ?? '0');
                $totalLigne += $duree;
            }

            // Ne pas comptabiliser les lignes vides
            if ($totalLigne == 0) continue;

            // Répartir selon le code de travail
            $codeTravail = $ligne['code_travail'];
            if ($codeTravail) {
                switch (strtoupper($codeTravail->code)) {
                    case 'FOR':
                        $totaux['total_heure_formation'] += $totalLigne;
                        break;
                    case 'DEP':
                        $totaux['total_heure_deplacement'] += $totalLigne;
                        break;
                    case 'CSN':
                        $totaux['total_heure_csn'] += $totalLigne;
                        break;
                    case 'CAISSE':
                        $totaux['total_heure_caisse'] += $totalLigne;
                        break;
                    case 'CONGE':
                        $totaux['total_heure_conge_mobile'] += $totalLigne;
                        break;
                    case 'HEURESUP':
                        $totaux['total_heure_supp'] += $totalLigne;
                        break;
                    default:
                        $totaux['total_heure_regulier'] += $totalLigne;
                }
            }
        }

        // Calculer le total général
        $totaux['total_heure'] = array_sum($totaux);

        $this->totaux = $totaux;
    }

    /**
     * Recalculer les totaux quand une durée change
     */
    public function updatedLignesTravail()
    {
        $this->calculerTotaux();
    }

    /**
     * Hook spécifique pour formater les valeurs lors de la saisie
     */
    public function updatedLignesTravailPropertyDuree($value, $key)
    {
        // $key sera au format "0.duree_1" par exemple
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $ligneIndex = $parts[0];
            $jourProperty = $parts[1];

            // Reformater la valeur saisie
            $formattedValue = $this->formatDecimalToDisplay($this->parseUserInputToDecimal($value));
            $this->lignesTravail[$ligneIndex][$jourProperty] = $formattedValue;
        }

        $this->calculerTotaux();
    }

    /**
     * Calculer les jours fériés pour la semaine
     */
    private function calculerJoursFeries()
    {
        // Logique simple - à enrichir selon vos besoins
        $this->joursFeries = []; // Pas de jours fériés pour l'instant
    }

    /**
     * Enregistrer les modifications (transition workflow: enregistrer)
     */
    public function enregistrer()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Appliquer la transition workflow si nécessaire
                if ($this->operation->canTransition('enregistrer')) {
                    $this->operation->applyTransition('enregistrer', [
                        'comment' => 'Feuille de temps mise à jour par ' . Auth::user()->name
                    ]);
                }

                // Sauvegarder les lignes de travail
                $this->sauvegarderLignesTravail();

                // Mettre à jour les totaux de l'opération
                $this->operation->update($this->totaux);
            });

            session()->flash('success', 'Feuille de temps enregistrée avec succès.');

        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de l\'enregistrement: ' . $th->getMessage());
        }
    }

    /**
     * Soumettre la feuille (transition workflow: soumettre)
     */
    public function soumettre()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Sauvegarder d'abord
                $this->sauvegarderLignesTravail();
                $this->operation->update($this->totaux);

                // Puis soumettre
                if ($this->operation->canTransition('soumettre')) {
                    $this->operation->applyTransition('soumettre', [
                        'comment' => 'Feuille de temps soumise par ' . Auth::user()->name
                    ]);
                } else {
                    throw new \Exception('Impossible de soumettre cette feuille de temps');
                }
            });

            session()->flash('success', 'Feuille de temps soumise avec succès.');
            return redirect()->route('feuille-temps.list');

        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la soumission: ' . $th->getMessage());
        }
    }

    /**
     * Sauvegarder les lignes de travail
     */
    private function sauvegarderLignesTravail()
    {
        foreach ($this->lignesTravail as $ligneData) {
            // Calculer le total des heures pour cette ligne
            $totalLigne = 0;
            $dataToSave = [
                'operation_id' => $this->operation->id,
                'codes_travail_id' => $ligneData['codes_travail_id'],
            ];

            // Convertir et sauvegarder chaque jour
            for ($jour = 0; $jour <= 6; $jour++) {
                $duree = $this->parseUserInputToDecimal($ligneData["duree_{$jour}"] ?? '0');
                $dataToSave["duree_{$jour}"] = $duree;
                $totalLigne += $duree;
            }

            if ($ligneData['id']) {
                // Mettre à jour ligne existante seulement si elle a des heures
                if ($totalLigne > 0) {
                    LigneTravail::where('id', $ligneData['id'])->update($dataToSave);
                } else {
                    // Supprimer si plus d'heures
                    LigneTravail::find($ligneData['id'])?->delete();
                }
            } else {
                // Créer nouvelle ligne seulement si elle a des heures
                if ($totalLigne > 0) {
                    LigneTravail::create($dataToSave);
                }
            }
        }
    }

    /**
     * Vérifier si une ligne peut être modifiée
     */
    public function peutModifierLigne($index)
    {
        return !($this->lignesTravail[$index]['auto_rempli'] ?? false);
    }

    /**
     * Vérifier si la feuille peut être modifiée
     */
    public function peutModifier()
    {
        return $this->operation->isEditable();
    }

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-edit');
    }
}
