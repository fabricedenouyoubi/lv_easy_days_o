<?php

namespace Modules\Entreprise\Livewire;

use Livewire\Component;
use Modules\Entreprise\Models\Entreprise;

class EntrepriseInfo extends Component
{
    public $entreprise;
    public $editing = false;
    public $name;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:100',
        'description' => 'nullable|string'
    ];

    protected $messages = [
        'name.required' => 'Le nom de l\'entreprise est obligatoire.',
        'name.max' => 'Le nom ne peut pas dépasser 100 caractères.'
    ];

    public function mount()
    {
        $this->loadEntreprise();
    }

    private function loadEntreprise()
    {
        $this->entreprise = Entreprise::first();
        
        if ($this->entreprise) {
            $this->name = $this->entreprise->name;
            $this->description = $this->entreprise->description;
        }
    }

    public function edit()
    {
        $this->editing = true;
    }

    public function cancel()
    {
        $this->editing = false;
        $this->loadEntreprise();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->entreprise) {
                // Modifier l'entreprise existante
                $this->entreprise->update([
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
            } else {
                // Créer nouvelle entreprise
                $this->entreprise = Entreprise::create([
                    'name' => $this->name,
                    'description' => $this->description,
                ]);
            }

            $this->editing = false;
            $this->loadEntreprise();
            
            session()->flash('success', 'Informations de l\'entreprise mises à jour avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('entreprise::livewire.entreprise-info');
    }
}
