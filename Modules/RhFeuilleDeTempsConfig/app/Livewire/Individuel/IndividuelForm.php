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
            'employe_id' => [
                'required',
                'exists:employes,id',
                'unique:configurations,employe_id,NULL,id,code_travail_id,' . $this->codeTravailId . ',annee_budgetaire_id,' . $this->getAnneeActiveId()
            ],
            'quota' => 'required|numeric|min:0|max:9999.99',
        ];
    }

    protected $messages = [
        'employe_id.required' => 'L\'employé est obligatoire.',
        'employe_id.exists' => 'L\'employé sélectionné n\'existe pas.',
        'employe_id.unique' => 'Cet employé a déjà une configuration pour ce code de travail cette année.',
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
            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                session()->flash('error', 'Aucune année financière active trouvée.');
                return;
            }

            $employe = Employe::findOrFail($this->employe_id);

            $data = [
                'libelle' => $employe->nom . ' ' . $employe->prenom, // Libellé automatique
                'quota' => $this->quota,
                'consomme' => 0, // Valeur par défaut
                'reste' => $this->quota, // Reste = quota au début
                'date' => null, // Pas de date pour individuel
                'commentaire' => '',
                'employe_id' => $this->employe_id,
                'annee_budgetaire_id' => $anneeBudgetaire->id,
                'code_travail_id' => $this->codeTravailId,
            ];

            if ($this->configurationId) {
                // Modification
                $configuration = Configuration::findOrFail($this->configurationId);
                
                // Recalculer le reste si le quota change
                $nouveauReste = $this->quota - $configuration->consomme;
                $data['reste'] = max(0, $nouveauReste); // Ne peut pas être négatif
                
                $configuration->update($data);
                
                $this->dispatch('configurationUpdated');
            } else {
                // Création
                Configuration::create($data);
                
                $this->dispatch('configurationCreated');
            }

            $this->reset(['employe_id', 'quota']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['employe_id', 'quota']);
        $this->dispatch('modalClosed');
    }

    public function getEmployesProperty()
    {
        return Employe::orderBy('nom')->orderBy('prenom')->get();
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.individuel.individuel-form', [
            'employes' => $this->employes,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive
        ]);
    }
}
