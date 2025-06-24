<?php

namespace Modules\Rh\Livewire\Employe;

use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Modules\Rh\Models\Employe\Employe;
use Modules\Rh\Models\Employe\HistoriqueGestionnaire;
use Modules\Rh\Models\Employe\HistoriqueHeuresSemaines;
use Spatie\Permission\Models\Role;

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
    public $gestionnaire_list;
    public $est_gestionnaire = false;

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
            'nombre_d_heure_semaine' => 'required|numeric|min:1|max:100',
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
            'nombre_d_heure_semaine.numeric' => 'Le nombre d’heures doit être un entier.',
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
        return Role::all();
    }

    /*
        - operation au montage du composant d'ajout de l'employe
        - chargement des groupes
    */
    public function mount()
    {
        $this->groups_list = $this->get_groups();
        $this->gestionnaire_list = Employe::where('est_gestionnaire', true)->get();
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

            //--- synchronisation des groupes/permission de l'utilisateur
            $user->syncRoles($this->groups);

            if (!$this->matricule) {
                $this->matricule = $this->generateMatricule();
            }

            $employe = Employe::create([
                'matricule' => $this->matricule,
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'date_de_naissance' => $this->date_de_naissance,
                'user_id' => $user->id,
                'entreprise_id' => $this->entreprise_id ?? null,
                'gestionnaire_id' => $this->gestionnaire_id ?? null,
                'adresse_id' => $this->adresse_id ?? null,
                'date_embauche' => Carbon::now(),
                'est_gestionnaire' => $this->est_gestionnaire,
            ]);

            //--- Sauvegarde de l'historique des gestionnaires d'un employe
            if ($this->gestionnaire_id) {
                HistoriqueGestionnaire::create([
                    'employe_id' => $employe->id,
                    'gestionnaire_id' => $this->gestionnaire_id,
                    'date_debut' => Carbon::now(),
                ]);
            }

            //--- Sauvegarde de l'historique des heures d'un employe
            HistoriqueHeuresSemaines::create([
                'employe_id' => $employe->id,
                'nombre_d_heure_semaine' => $this->nombre_d_heure_semaine,
                'date_debut' => Carbon::now(),
            ]);

            $this->dispatch('employeCreated');
        } catch (\Throwable $th) {
            dd($th->getMessage());
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
            'rh::livewire.employe.employe-form'
        );
    }
}
