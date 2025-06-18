<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Collectif;

use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class CollectifForm extends Component
{
    public $configurationId;
    public $codeTravailId;
    public $libelle;
    public $quota;

    protected $listeners = ['editConfiguration'];

    protected function rules()
    {
        return [
            'libelle' => 'required|string|max:200',
            'quota' => 'required|numeric|min:0|max:9999.99',
        ];
    }

    protected $messages = [
        'libelle.required' => 'Le libellé est obligatoire.',
        'libelle.max' => 'Le libellé ne peut pas dépasser 200 caractères.',
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
            $configuration = Configuration::findOrFail($this->configurationId);
            
            $this->libelle = $configuration->libelle;
            $this->quota = $configuration->quota;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                session()->flash('error', 'Aucune année financière active trouvée.');
                return;
            }

            $data = [
                'libelle' => $this->libelle,
                'quota' => $this->quota,
                'consomme' => 0, // Valeur par défaut
                'reste' => $this->quota, // Reste = quota au début
                'date' => null, // Pas de date pour collectif
                'commentaire' => '',
                'employe_id' => null, // Pas d'employé unique pour collectif
                'annee_budgetaire_id' => $anneeBudgetaire->id,
                'code_travail_id' => $this->codeTravailId,
            ];

            if ($this->configurationId) {
                // Modification
                $configuration = Configuration::findOrFail($this->configurationId);
                
                // Recalculer le reste en fonction du nouveau quota
                $totalConsomme = $configuration->total_consomme_individuel;
                $data['consomme'] = $totalConsomme;
                $data['reste'] = max(0, $this->quota - $totalConsomme);
                
                $configuration->update($data);
                
                $this->dispatch('configurationUpdated');
            } else {
                // Création
                Configuration::create($data);
                
                $this->dispatch('configurationCreated');
            }

            $this->reset(['libelle', 'quota']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['libelle', 'quota']);
        $this->dispatch('modalClosed');
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.collectif.collectif-form', [
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive
        ]);
    }
}
