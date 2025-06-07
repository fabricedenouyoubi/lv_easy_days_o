<?php

namespace Modules\Rh\Livewire;

use Livewire\Component;
use Modules\Rh\Models\Poste;

class PosteForm extends Component
{
    public $libelle;
    public $description;
    public $actif;
    public $posteId;

    protected function rules()
    {
        return [
            'libelle' => 'required|max:126',
            'description' => 'required|max:126',
        ];
    }


    protected $messages = [
        'libelle.required' => 'le libelle est obligatoire',
        'libelle.max' => 'le libelle doit avoir maximum :max caractère',
        'description.required' => 'la description est obligatoire',
        'description.max' => 'la description doit avoir maximum :max caractère',
    ];

    public function mount($posteId = null)
    {
        $this->posteId = $posteId;
        if ($this->posteId) {
            $poste = Poste::findOrFail($this->posteId);
            $this->libelle = $poste->libelle;
            $this->description = $poste->description;
            $this->actif = $poste->actif;
        }
    }

    public function save()
    {
        $this->validate();
        //dd($this->posteId);
        try {

            if ($this->posteId == null) {
                $poste = Poste::create([
                    'libelle' => $this->libelle,
                    'description' => $this->description
                ]);
                $this->dispatch('posteCreated');
            } else {
                $poste = Poste::findOrFail($this->posteId);
                $poste->update([
                    'libelle' => $this->libelle,
                    'description' => $this->description,
                    'actif' => $this->actif
                ]);
                $this->dispatch('posteUpdated');
            }

            //$this->reset(['libelle', 'description', 'actif']);
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $th->getMessage());
        }
    }

    public function cancel()
    {
        $this->dispatch('showModal', false);
    }

    public function render()
    {
        return view('rh::livewire.poste-form');
    }
}
