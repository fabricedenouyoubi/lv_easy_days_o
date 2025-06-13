<?php

namespace Modules\RhEmploye\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Modules\RhEmploye\Models\Employe;

class EmployeForm extends Component
{
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
    public $groups_list;

    //--- Règles de validation pour l'ajout d'un employe
    protected function rules()
    {
        return [
            'matricule' => 'nullable|string|max:100|unique:employes,matricule',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_de_naissance' => 'nullable|date|before:today',
            'entreprise_id' => 'nullable|exists:entreprises,id',
            'gestionnaire_id' => 'nullable|exists:employes,id',
            'nombre_d_heure_semaine' => 'required|integer|min:1|max:100',
            'email' => 'required|email|unique:users,email',
            'groups' => 'required'
        ];
    }

    //--- Messages de validation pour l'ajout d'un employe
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
            'groups.required' => 'Au moins un groupe doit être sélectionné.'
        ];
    }

    //--- recuperation des groupes
    public function get_groups()
    {
        return Group::all();
    }

    /*
        - operation au montage du composant d'ajout de l'employe
        - chargement des groupes
    */
    public function mount()
    {
        $this->groups_list = $this->get_groups();
    }

    //--- fonction de generation du matricule d'un employé
    public function generateMatricule()
    {
        $employeCount = Employe::count() + 1;
        $current_year = date('Y');
        return 'EMP' . $current_year . '-00' . $employeCount;
    }

    //---fonction d'ajout d'un employe
    public function save()
    {
        $this->validate();

        try {
            $user =  User::create([
                'email' => $this->email,
                'password' => 'password',
                'name' => $this->nom . ' ' . $this->prenom,
            ]);

            $user->groups()->attach($this->groups);

            //mise a jour des permission
            $groups = $user->groups;
            foreach ($groups as $group) {
                $user->permissions()->attach($group->permissions->pluck('id')->toArray());
            }

            if (!$this->matricule) {
                $this->matricule = $this->generateMatricule();
            }

            Employe::create([
                'matricule' => $this->matricule,
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'date_de_naissance' => $this->date_de_naissance,
                'user_id' => $user->id,
                'entreprise_id' => $this->entreprise_id ?? null,
                'gestionnaire_id' => $this->gestionnaire_id ?? null,
                'nombre_d_heure_semaine' => $this->nombre_d_heure_semaine,
                'adresse_id' => $this->adresse_id ?? null,
                'date_embauche' => Carbon::now()
            ]);

            $this->dispatch('employeCreated');
        } catch (\Throwable $th) {

            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $th->getMessage());
        }
    }

    //--- fermeture du formulaire d'ajout d'un employe
    public function cancel()
    {
        //$this->dispatch('showModal', false);
        $this->reset(['matricule', 'nom', 'prenom', 'date_de_naissance', 'entreprise_id', 'gestionnaire_id', 'nombre_d_heure_semaine', 'adresse_id', 'email', 'groups']);
    }

    //--- chargement du formulaire d'ajout d'un employe
    public function render()
    {
        return view(
            'rhemploye::livewire.employe-form'
        );
    }
}
