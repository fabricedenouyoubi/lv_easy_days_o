<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Collectif;

use Livewire\Component;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class AffectationEmployes extends Component
{
    public $configurationId;
    public $configuration;
    public $employesSelectionnes = [];
    public $searchEmploye = '';

    protected $listeners = ['loadAffectation'];

    public function mount($configurationId = null)
    {
        $this->configurationId = $configurationId;
        $this->loadAffectation();
    }

    public function loadAffectation($configurationId = null)
    {
        if ($configurationId) {
            $this->configurationId = $configurationId;
        }

        if ($this->configurationId) {
            $this->configuration = Configuration::with('employes')->findOrFail($this->configurationId);
            
            // Charger les employés déjà affectés
            $this->employesSelectionnes = $this->configuration->employes->pluck('id')->toArray();
        }
    }

    public function toggleEmploye($employeId)
    {
        if (in_array($employeId, $this->employesSelectionnes)) {
            // Désélectionner
            $this->employesSelectionnes = array_filter($this->employesSelectionnes, function($id) use ($employeId) {
                return $id != $employeId;
            });
        } else {
            // Sélectionner
            $this->employesSelectionnes[] = $employeId;
        }
    }

    public function sauvegarderAffectations()
    {
        try {
            if (!$this->configuration) {
                session()->flash('error', 'Configuration non trouvée.');
                return;
            }

            // Préparer les données pour la synchronisation
            $syncData = [];
            foreach ($this->employesSelectionnes as $employeId) {
                // Récupérer la consommation existante ou initialiser à 0
                $existingPivot = $this->configuration->employes()->where('employe_id', $employeId)->first();
                $consommeIndividuel = $existingPivot ? $existingPivot->pivot->consomme_individuel : 0;
                
                $syncData[$employeId] = [
                    'consomme_individuel' => $consommeIndividuel,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Synchroniser les employés affectés
            $this->configuration->employes()->sync($syncData);

            // Recalculer les totaux
            $this->recalculerTotaux();

            $this->dispatch('employesAffected');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    private function recalculerTotaux()
    {
        // Recalculer le total consommé et le reste
        $totalConsomme = $this->configuration->employes()->sum('configuration_employe.consomme_individuel');
        
        $this->configuration->update([
            'consomme' => $totalConsomme,
            'reste' => max(0, $this->configuration->quota - $totalConsomme)
        ]);

        // Recharger la configuration avec les nouvelles données
        $this->configuration->refresh();
    }

    public function cancel()
    {
        $this->dispatch('modalClosed');
    }

    public function getEmployesDisponiblesProperty()
    {
        return Employe::when($this->searchEmploye, function ($query) {
                $query->where('nom', 'like', '%' . $this->searchEmploye . '%')
                      ->orWhere('prenom', 'like', '%' . $this->searchEmploye . '%')
                      ->orWhere('matricule', 'like', '%' . $this->searchEmploye . '%');
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();
    }

    public function getNombreEmployesSelectionnesProperty()
    {
        return count($this->employesSelectionnes);
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.collectif.affectation-employes', [
            'employesDisponibles' => $this->employesDisponibles,
            'nombreEmployesSelectionnes' => $this->nombreEmployesSelectionnes
        ]);
    }
}