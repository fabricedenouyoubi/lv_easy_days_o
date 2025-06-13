<?php

namespace Modules\RhEmploye\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\RhEmploye\Models\Employe;

class EmployeEditForm extends Component
{
    public $employeId;
    public $matricule;
    public $nom;
    public $prenom;
    public $date_de_naissance;
    public $user_id;
    public $entreprise_id;
    public $gestionnaire_id;
    public $nombre_d_heure_semaine;
    public $adresse_id;
    public $email;
    public $groups;
    public $groups_array;
    public $showModal = false;
    public $groups_list;

    //--- recuperation des groupes
    public function get_groups()
    {
        return Group::all();
    }

    /*
        - operation au montage du composant d'edition d'un employe
        - chargement infos de l'employe
    */
    public function mount()
    {
        if ($this->employeId) {
            $employe = Employe::findOrFail($this->employeId);
            $this->matricule = $employe->matricule;
            $this->nom = $employe->nom;
            $this->prenom = $employe->prenom;
            $this->date_de_naissance = $employe->date_de_naissance;
            $this->user_id = $employe->user_id;
            $this->entreprise_id = $employe->entreprise_id;
            $this->gestionnaire_id = $employe->gestionnaire_id;
            $this->nombre_d_heure_semaine = $employe->nombre_d_heure_semaine;
            $this->adresse_id = $employe->adresse_id;
            $this->email = $employe->email();
            $this->groups = $employe->employe_groups()->pluck('id')->toArray();
        }
        $this->groups_list = $this->get_groups();
    }

    //--- Règles de validation pour la modification  d'un employe
    protected function rules()
    {
        return [
            'matricule' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('employes', 'matricule')->ignore($this->employeId)
            ],
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'nullable|date|before:today',
            'entreprise_id' => 'nullable|exists:entreprises,id',
            'gestionnaire_id' => 'nullable|exists:employes,id',
            'nombre_d_heure_semaine' => 'required|integer|min:1|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->user_id)
            ],
            'groups' => 'required|array|min:1',
        ];
    }

    //--- Messages de validation pour la modification d'un employe
    protected function messages()
    {
        return [
            'matricule.unique' => 'Ce matricule est déjà utilisé.',
            'matricule.max' => 'Le matricule ne peut pas dépasser 100 caractères.',
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'date_de_naissance.date' => 'La date de naissance doit être une date valide.',
            'date_de_naissance.before' => 'La date de naissance doit être antérieure à aujourd\'hui.',
            'entreprise_id.exists' => 'L\'entreprise sélectionnée est invalide.',
            'gestionnaire_id.exists' => 'Le gestionnaire sélectionné est invalide.',
            'nombre_d_heure_semaine.integer' => 'Le nombre d’heures doit être un entier.',
            'nombre_d_heure_semaine.required' => 'Le nombre d’heures est obligatoire.',
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'L’adresse e-mail doit être valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'groups.required' => 'Au moins un groupe doit être sélectionné.',
        ];
    }

    //--- fonction de generation du matricule d'un employé
    public function generateMatricule()
    {
        $employeCount = Employe::count() + 1;
        $current_year = date('Y');
        return 'EMP' . $current_year . '-00' . $employeCount;
    }

    //---fonction de modification d'un employe
    public function save()
    {
        $this->validate();

        try {

            $employe = Employe::findOrFail($this->employeId);
            $user = User::findOrFail($employe->user_id);

            //--- mise a jour coompte utilisateur
            $user->update([
                'email' => $this->email,
                'name' => $this->nom . ' ' . $this->prenom,
            ]);

            //--- mise a jour des groupes
            $user->groups()->sync($this->groups);

            //--- mise a jour des permission
            $groupPermisionsId = collect();
            foreach ($user->groups as $group) {
                $groupPermisionsId = $groupPermisionsId->merge($group->permissions->pluck('id'));
            }
            $uniquePermissionId = $groupPermisionsId->unique()->toArray();
            $user->permissions()->sync($uniquePermissionId);

            $employe->update([
                'matricule' => $this->matricule,
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'date_de_naissance' => $this->date_de_naissance,
                'entreprise_id' => $this->entreprise_id,
                'gestionnaire_id' => $this->gestionnaire_id,
                'nombre_d_heure_semaine' => $this->nombre_d_heure_semaine,
                'adresse_id' => $this->adresse_id,
            ]);

            $this->dispatch('employeUpdated');
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de l’enregistrement : ' . $th->getMessage());
        }
    }

    //--- fermeture du formulaire de modification d'un employe
    public function cancel()
    {
        if ($this->employeId) {
            $employe = Employe::findOrFail($this->employeId);
            $this->matricule = $employe->matricule;
            $this->nom = $employe->nom;
            $this->prenom = $employe->prenom;
            $this->date_de_naissance = $employe->date_de_naissance;
            $this->user_id = $employe->user_id;
            $this->entreprise_id = $employe->entreprise_id;
            $this->gestionnaire_id = $employe->gestionnaire_id;
            $this->nombre_d_heure_semaine = $employe->nombre_d_heure_semaine;
            $this->adresse_id = $employe->adresse_id;
            $this->email = $employe->email();
            $this->groups = $employe->employe_groups()->pluck('id')->toArray();
        }
        $this->groups_list = $this->get_groups();
    }

    public function render()
    {
        return view('rhemploye::livewire.employe-edit-form');
    }
}
