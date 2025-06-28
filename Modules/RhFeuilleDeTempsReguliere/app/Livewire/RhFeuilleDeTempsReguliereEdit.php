<?php

namespace Modules\RhFeuilleDeTempsReguliere\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsReguliere\Models\LigneTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

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
    
    // Jours de la semaine
    public $joursLabels = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    public $joursFeries = [];

    protected $rules = [
        'lignesTravail.*.codes_travail_id' => 'required|exists:codes_travail,id',
        'lignesTravail.*.duree_0' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_1' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_2' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_3' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_4' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_5' => 'nullable|numeric|min:0|max:12',
        'lignesTravail.*.duree_6' => 'nullable|numeric|min:0|max:12',
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
            // $this->chargerCodesTravauxDisponibles();
            
            // Charger les lignes de travail existantes
            $this->chargerLignesTravail();
            
            // Calculer les jours fériés
            $this->calculerJoursFeries();
            
            // Calculer les totaux
            $this->calculerTotaux();
            
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors du chargement: ' . $th->getMessage());
        }
    }

    /**
     * Charger les codes de travail disponibles
     */
    private function chargerCodesTravauxDisponibles()
    {
        // Récupérer les codes selon les catégories (exclure les absences)
        $categoriesExclues = ['Absence']; // À adapter selon votre logique
        
        $this->codesTravauxDisponibles = CodeTravail::with('categorie')
            ->whereHas('categorie', function($query) use ($categoriesExclues) {
                $query->whereNotIn('intitule', $categoriesExclues);
            })
            ->orderBy('libelle')
            ->get()
            ->groupBy('categorie.intitule');
    }

    /**
     * Charger les lignes de travail existantes ou créer des lignes vides
     */
    private function chargerLignesTravail()
    {
        $lignesExistantes = $this->operation->lignesTravail;
        
        if ($lignesExistantes->count() > 0) {
            // Charger les lignes existantes
            foreach ($lignesExistantes as $ligne) {
                $this->lignesTravail[] = [
                    'id' => $ligne->id,
                    'codes_travail_id' => $ligne->codes_travail_id,
                    'duree_0' => $ligne->duree_0 ?? 0,
                    'duree_1' => $ligne->duree_1 ?? 0,
                    'duree_2' => $ligne->duree_2 ?? 0,
                    'duree_3' => $ligne->duree_3 ?? 0,
                    'duree_4' => $ligne->duree_4 ?? 0,
                    'duree_5' => $ligne->duree_5 ?? 0,
                    'duree_6' => $ligne->duree_6 ?? 0,
                    'auto_rempli' => $ligne->auto_rempli ?? false,
                ];
            }
        } else {
            // Créer une ligne vide pour commencer
            $this->ajouterLigneTravail();
        }
    }

    /**
     * Ajouter une nouvelle ligne de travail
     */
    public function ajouterLigneTravail()
    {
        $this->lignesTravail[] = [
            'id' => null,
            'codes_travail_id' => null,
            'duree_0' => 0,
            'duree_1' => 0,
            'duree_2' => 0,
            'duree_3' => 0,
            'duree_4' => 0,
            'duree_5' => 0,
            'duree_6' => 0,
            'auto_rempli' => false,
        ];
    }

    /**
     * Supprimer une ligne de travail
     */
    public function supprimerLigneTravail($index)
    {
        if (isset($this->lignesTravail[$index])) {
            // Si la ligne a un ID, la marquer pour suppression en base
            if ($this->lignesTravail[$index]['id']) {
                LigneTravail::find($this->lignesTravail[$index]['id'])?->delete();
            }
            
            unset($this->lignesTravail[$index]);
            $this->lignesTravail = array_values($this->lignesTravail); // Réindexer
            
            $this->calculerTotaux();
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
            if (!$ligne['codes_travail_id']) continue;
            
            // Calculer le total des heures pour cette ligne
            $totalLigne = 0;
            for ($jour = 0; $jour <= 6; $jour++) {
                $totalLigne += floatval($ligne["duree_{$jour}"] ?? 0);
            }
            
            // Répartir selon le code de travail
            $codeTravail = CodeTravail::find($ligne['codes_travail_id']);
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
            if (!$ligneData['codes_travail_id']) continue;
            
            $data = [
                'operation_id' => $this->operation->id,
                'codes_travail_id' => $ligneData['codes_travail_id'],
                'duree_0' => $ligneData['duree_0'] ?? 0,
                'duree_1' => $ligneData['duree_1'] ?? 0,
                'duree_2' => $ligneData['duree_2'] ?? 0,
                'duree_3' => $ligneData['duree_3'] ?? 0,
                'duree_4' => $ligneData['duree_4'] ?? 0,
                'duree_5' => $ligneData['duree_5'] ?? 0,
                'duree_6' => $ligneData['duree_6'] ?? 0,
            ];
            
            if ($ligneData['id']) {
                // Mettre à jour ligne existante
                LigneTravail::where('id', $ligneData['id'])->update($data);
            } else {
                // Créer nouvelle ligne
                LigneTravail::create($data);
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
