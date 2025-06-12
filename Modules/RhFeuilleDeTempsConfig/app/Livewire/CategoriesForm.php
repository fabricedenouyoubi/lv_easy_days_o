<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire;

use Livewire\Component;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

class CategoriesForm extends Component
{
  public $categorieId;
    public $intitule;
    public $configurable = false;
    public $valeur_config;

    protected $listeners = ['editCategorie'];

    protected function rules()
    {
        $rules = [
            'intitule' => [
                'required',
                'string',
                'max:100',
                'unique:categories,intitule'
            ],
            'configurable' => 'boolean',
            'valeur_config' => 'nullable|in:Individuel,Collectif,Jour',
        ];

        // Si on modifie, exclure l'ID actuel de la validation d'unicité
        if ($this->categorieId) {
            $rules['intitule'][] = $this->categorieId;
            $rules['intitule'] = [
                'required',
                'string',
                'max:100',
                'unique:categories,intitule,' . $this->categorieId
            ];
        }

        // Si configurable est false, valeur_config doit être null
        if (!$this->configurable) {
            $rules['valeur_config'] = 'nullable';
        } else {
            $rules['valeur_config'] = 'required|in:Individuel,Collectif,Jour';
        }

        return $rules;
    }

    protected $messages = [
        'intitule.required' => 'L\'intitulé de la catégorie est obligatoire.',
        'intitule.unique' => 'Cette catégorie existe déjà.',
        'intitule.max' => 'L\'intitulé ne peut pas dépasser 100 caractères.',
        'valeur_config.required' => 'La valeur de configuration est obligatoire quand la catégorie est configurable.',
        'valeur_config.in' => 'La valeur de configuration doit être : Individuel, Collectif ou Jour.',
    ];

    public function mount($categorieId = null)
    {
        $this->categorieId = $categorieId;
        
        if ($categorieId) {
            $this->loadCategorie();
        }
    }

    public function editCategorie($categorieId)
    {
        $this->categorieId = $categorieId;
        $this->loadCategorie();
    }

    private function loadCategorie()
    {
        if ($this->categorieId) {
            $categorie = Categorie::findOrFail($this->categorieId);
            
            $this->intitule = $categorie->intitule;
            $this->configurable = $categorie->configurable;
            $this->valeur_config = $categorie->valeur_config;
        }
    }

    public function updatedConfigurable()
    {
        // Si configurable devient false, vider valeur_config
        if (!$this->configurable) {
            $this->valeur_config = null;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'intitule' => $this->intitule,
                'configurable' => $this->configurable,
                'valeur_config' => $this->configurable ? $this->valeur_config : null,
            ];

            if ($this->categorieId) {
                // Modification
                $categorie = Categorie::findOrFail($this->categorieId);
                $categorie->update($data);
                
                $this->dispatch('categorieUpdated');
            } else {
                // Création
                Categorie::create($data);
                
                $this->dispatch('categorieCreated');
            }

            $this->reset();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset();
        $this->dispatch('modalClosed');
    }

    public function getValeurConfigOptions()
    {
        return Categorie::getValeurConfigOptions();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.categories-form', [
            'valeurConfigOptions' => $this->getValeurConfigOptions()
        ]);
    }

    

}
