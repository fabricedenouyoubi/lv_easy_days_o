<?php

namespace App\Livewire;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class GroupeForm extends Component
{
    public $groupId;
    public $name;


    // Définition des règles de validation
    protected $rules = [
        'name' => 'required|min:3',
    ];

    protected $messages = [
        'name.required' => 'Le nom du groupe est obligatoire. Veuillez le renseigner.',
        'name.min' => 'Le nom du groupe doit contenir au moins :min caractères.',

    ];

    //--- chargement du nom du groupe en cas de modification
    public function mount()
    {
        if ($this->groupId) {
            $group = Role::findOrFail($this->groupId);
            $this->name = $group->name;
        }
    }

    //--- fonction d'ajout ou de mofication d'un groupe
    public function save()
    {
        $this->validate();
        $user_connect = Auth::user();

        try {
            if ($this->groupId) {
                if (Role::where('name', $this->name)->where('id', '!=', $this->groupId)->exists()) {
                    $this->addError('name', 'Un groupe avec ce nom existe déjà.');
                    return;
                }
                $group = Role::findOrFail($this->groupId);

                $old_group_name = $group->name;

                $group->update(['name' => $this->name]);

                //--- Journalisation
                Log::channel('daily')->info("Le groupe d'utilisateur " . $old_group_name . " vient d'être modifié par l' utilisateur " . $user_connect->name . " en " . $group->name);

                $this->dispatch('groupUpdated');
            } else {
                if (Role::where('name', $this->name)->exists()) {
                    $this->addError('name', 'Un groupe avec ce nom existe déjà.');
                    return;
                }
                $group = Role::create(['name' => $this->name]);

                //--- Journalisation
                Log::channel('daily')->info("Le groupe d'utilisateur " . $group->name . " vient d'être ajouté par l' utilisateur " . $user_connect->name);

                $this->dispatch('groupCreated');
            }
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la sauvegarde du groupe  " . $this->name . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $th->getMessage());
        }
    }

    public function cancel()
    {
        if ($this->groupId) {
            $this->name = Role::findOrFail($this->groupId)->name;
        } else {
            $this->reset('name');
        }
    }

    public function render()
    {
        return view('livewire.groupe-form');
    }
}
