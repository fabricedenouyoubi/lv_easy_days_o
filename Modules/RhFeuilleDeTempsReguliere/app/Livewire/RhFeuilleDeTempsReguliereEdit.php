<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class RhFeuilleDeTempsReguliereEdit extends Component
{
    public $operationId;
    public $semaineId;
    public $operation;
    public $semaine;
    public $employe;

    // Calcul totaux
    public $totauxrecapitulatif = [];
    public $totalGeneral = 0;

    public $heureSupplementaireAjuste = '00.00';
    public $heureSupplementaireAPayer = '00.00';

    // Données des lignes de travail
    public $lignesTravail = [];
    public $codesTravauxDisponibles = [];

    public $datesSemaine = [];
    // Jours de la semaine avec dates complètes
    public $joursFeries = [];

    // Banque de temps
    public $banqueDeTemps = [];

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



    protected $rules = [
        'lignesTravail.*.duree_0' => 'nullable|string|max:5',
        'lignesTravail.*.duree_1' => 'nullable|string|max:5',
        'lignesTravail.*.duree_2' => 'nullable|string|max:5',
        'lignesTravail.*.duree_3' => 'nullable|string|max:5',
        'lignesTravail.*.duree_4' => 'nullable|string|max:5',
        'lignesTravail.*.duree_5' => 'nullable|string|max:5',
        'lignesTravail.*.duree_6' => 'nullable|string|max:5',
        'heureSupplementaireAjuste' => 'nullable|string|max:5',
        'heureSupplementaireAPayer' => 'nullable|string|max:5',
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
            $this->calculerRecapitulatif();

            // Calculer la heure supplémentaire ajusté et à payer
            $this->chargerHeuresSupplementaires();

            // Calculer la banque de temps
            $this->calculerBanqueDeTemps();
            
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
                'is_dimanche' => $date->isSunday(),
                'jour_nom' => $date->locale('fr')->dayName,
                'jour_court' => $date->locale('fr')->shortDayName
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
     * Calculer le récapitulatif dynamique basé sur les lignes saisies
     */
    public function calculerRecapitulatif()
    {
        $recapitulatif = [];
        $totalGeneral = 0;

        foreach ($this->lignesTravail as $ligne) {
            $codeTravail = $ligne['code_travail'];

            // Calculer le total des heures pour cette ligne
            $totalLigne = 0;
            for ($jour = 0; $jour <= 6; $jour++) {
                $duree = $this->parseUserInputToDecimal($ligne["duree_{$jour}"] ?? '0');
                $totalLigne += $duree;
            }

            // Ajouter au récapitulatif seulement si il y a des heures
            if ($totalLigne > 0) {
                $recapitulatif[] = [
                    'code_travail' => $codeTravail,
                    'total_heures' => $totalLigne
                ];

                $totalGeneral += $totalLigne;
            }
        }

        // Les heures supplémentaires au total
        $heureSupAjuste = $this->parseUserInputToDecimal($this->heureSupplementaireAjuste);
        $heureSupAPayer = $this->parseUserInputToDecimal($this->heureSupplementaireAPayer);

        $totalGeneral += $heureSupAjuste + $heureSupAPayer;

        // Trier par libellé
        usort($recapitulatif, function ($a, $b) {
            return strcmp($a['code_travail']->libelle, $b['code_travail']->libelle);
        });

        $this->totauxrecapitulatif = $recapitulatif;
        $this->totalGeneral = $totalGeneral;
    }

    /**
     * Recalculer les totaux quand une durée change
     */
    public function updatedLignesTravail()
    {
        $this->calculerRecapitulatif();
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

        // Recalculer les totaux
        $this->calculerRecapitulatif();
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
                $this->operation->update([
                    'total_heure' => $this->totalGeneral,
                    'total_heure_supp_ajuster' => $this->parseUserInputToDecimal($this->heureSupplementaireAjuste),
                    'total_heure_sup_a_payer' => $this->parseUserInputToDecimal($this->heureSupplementaireAPayer)
                ]);
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
                // Mise a jour totaux
                $this->operation->update([
                    'total_heure' => $this->totalGeneral,
                    'total_heure_supp_ajuster' => $this->parseUserInputToDecimal($this->heureSupplementaireAjuste),
                    'total_heure_sup_a_payer' => $this->parseUserInputToDecimal($this->heureSupplementaireAPayer)
                ]);

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

    /**
     * Charger les heures supplémentaires existantes
     */
    private function chargerHeuresSupplementaires()
    {
        $this->heureSupplementaireAjuste = $this->formatDecimalToDisplay($this->operation->total_heure_supp_ajuster ?? 0);
        $this->heureSupplementaireAPayer = $this->formatDecimalToDisplay($this->operation->total_heure_sup_a_payer ?? 0);
    }

    /**
     * Listener pour recalculer quand les heures supplémentaires changent
     */
    public function updatedHeureSupplementaireAjuste($value)
    {
        $this->heureSupplementaireAjuste = $this->formatDecimalToDisplay($this->parseUserInputToDecimal($value));
        $this->calculerRecapitulatif();
    }

    /**
     * Listener pour recalculer quand les heures supplémentaires à payer changent
     */
    public function updatedHeureSupplementaireAPayer($value)
    {
        $this->heureSupplementaireAPayer = $this->formatDecimalToDisplay($this->parseUserInputToDecimal($value));
        $this->calculerRecapitulatif();
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

    public function render()
    {
        return view('rhfeuilledetempsreguliere::livewire.rh-feuille-de-temps-reguliere-edit');
    }
}
