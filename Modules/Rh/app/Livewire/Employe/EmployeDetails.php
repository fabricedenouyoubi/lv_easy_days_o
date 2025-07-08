<?php

namespace Modules\Rh\Livewire\Employe;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Rh\Models\Employe\Employe;
use Modules\Rh\Models\Employe\HistoriqueGestionnaire;
use Modules\Rh\Models\Employe\HistoriqueHeuresSemaines;

class EmployeDetails extends Component
{
    use WithPagination;

    public $employeId;
    public $employe;
    public $showModal = false;
    public $showGestM = false;
    public $showHeuresM = false;

    public $countHistGestio = 0;
    public $countHistHeures = 0;


    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public $historique_gestionnaire;

    protected $paginationTheme = 'bootstrap';

    //--- ecouteur d'evenement venamt des composants enfants
    protected $listeners = [
        'closeEditModal' => 'closeModal',
        'closeGestModal' => 'closeGestModal',
        'employeUpdated' => 'handleEmployeUpdated',
        'gestionnaireAjoute' => 'gestionnaireAjoute',
        'HeureAjoute' => 'HeureAjoute'
    ];


    /*
        - operation au montage du composant d'affichage des informations de l'employe
        - chargement des employes et de leur gestionnaire
    */
    public function mount()
    {
        $this->employe =  Employe::with('gestionnaire')->findOrFail($this->employeId);
        $this->countHistGestio = HistoriqueGestionnaire::where('employe_id', $this->employeId)->count();
        $this->countHistHeures = HistoriqueHeuresSemaines::where('employe_id', $this->employeId)->count();
    }

    //--- Affichage du formulaire de modification des infos d'un employe
    public function showEditModal()
    {
        $this->showModal = !$this->showModal;
    }

    //--- fermeture du formulaire de modification des infos d'un employe
    public function closeModal($val = null)
    {
        $val ? $this->showModal = $this->val : $this->showModal = !$this->showModal;
    }

    //--- Affichage du formulaire ajout/modification d'un gestionnaire
    public function showGestModal()
    {
        $this->showGestM = !$this->showGestM;
    }

    //--- Affichage du formulaire d'ajout/mofication des heures d'un employe
    public function showHeuresModal()
    {
        $this->showHeuresM = !$this->showHeuresM;
    }

    //--- fermeture du formulaire d'ajout d'un gestionnaire
    public function closeGestModal($val = null)
    {
        $val ? $this->showGestM = $this->val : $this->showGestM = !$this->showGestM;
    }

    //--- fonction d'affichage du message de modification d'un employe
    public function handleEmployeUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Les informations de l\'employé ont été modifiés avec succès.');
    }

    //--- fonction d'affichage du message d'ajout/modification des infos du gestionnaire d'un employe
    public function gestionnaireAjoute()
    {
        $this->closeGestModal();
        session()->flash('success', 'Le nouveau gestionaire a été ajouté avec succès.');
    }

    //--- fonction d'affichage du message d'ajout d'un employe
    public function HeureAjoute()
    {
        $this->showHeuresM = !$this->showHeuresM;
        session()->flash('success', 'L\'heure a été mis à jour avec succès.');
    }

    //---  recuperation de l'historique des gestionnaires d'un employe
    public function get_historique_gestionnaire()
    {
        return HistoriqueGestionnaire::with('gestionnaire')->where('employe_id', $this->employeId)->paginate(10, ['*'], 'historique_gestionnaire');
    }

    //---  recuperation de l'historique des heures par semaine d'un employe
    public function get_historique_heure_par_semaine()
    {
        return HistoriqueHeuresSemaines::where('employe_id', $this->employeId)->paginate(10, ['*'], 'historique_heure');
    }

    public function render()
    {

        return view(
            'rh::livewire.employe.employe-details',
            [
                'gestionnaire_historique' => $this->get_historique_gestionnaire(),
                'heure_historique' => $this->get_historique_heure_par_semaine()
            ]
        );
    }
}
