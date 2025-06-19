<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Individuel;

use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class IndividuelForm extends Component
{
    public $configurationId;
    public $codeTravailId;
    public $employe_id;
    public $quota;

    protected $listeners = ['editConfiguration'];

    protected function rules()
    {
        return [
            'quota' => 'required|numeric|min:0|max:9999.99',
        ];
    }

    protected $messages = [
        'quota.required' => 'Le nombre d\'heures est obligatoire.',
        'quota.numeric' => 'Le nombre d\'heures doit être un nombre.',
        'quota.min' => 'Le nombre d\'heures doit être positif.',
        'quota.max' => 'Le nombre d\'heures ne peut pas dépasser 9999.99.',
    ];

    public function mount($configurationId = null, $codeTravailId = null)
    {
        $this->configurationId = $configurationId;
        $this->codeTravailId = $codeTravailId;
        
        if ($configurationId) {
            $this->loadConfiguration();
        }
    }

    public function editConfiguration($configurationId)
    {
        $this->configurationId = $configurationId;
        $this->loadConfiguration();
    }

    private function loadConfiguration()
    {
        if ($this->configurationId) {
            $configuration = Configuration::with('employe')->findOrFail($this->configurationId);
            
            $this->employe_id = $configuration->employe_id;
            $this->quota = $configuration->quota;
        }
    }

    private function getAnneeActiveId()
    {
        $annee = AnneeFinanciere::where('actif', true)->first();
        return $annee ? $annee->id : null;
    }

    public function save()
    {
        $this->validate();

        try {
            if (!$this->configurationId) {
                session()->flash('error', 'Configuration non trouvée.');
                return;
            }

            // Modification uniquement (plus de création)
            $configuration = Configuration::findOrFail($this->configurationId);
            
            // Recalculer le reste si le quota change
            $nouveauReste = $this->quota - $configuration->consomme;
            
            $configuration->update([
                'quota' => $this->quota,
                'reste' => max(0, $nouveauReste), // Ne peut pas être négatif
            ]);
            
            $this->dispatch('configurationUpdated');
            $this->reset(['quota']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['quota']);
        $this->dispatch('modalClosed');
    }

    public function getConfigurationProperty()
    {
        if ($this->configurationId) {
            return Configuration::with('employe')->find($this->configurationId);
        }
        return null;
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.individuel.individuel-form', [
            'configuration' => $this->configuration,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive
        ]);
    }
}
