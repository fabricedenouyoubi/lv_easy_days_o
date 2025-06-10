<?php

namespace Modules\RhEmploye\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Modules\RhEmploye\Models\Employe;
use Modules\RhEmploye\Models\HistoriqueGestionnaire;
use Livewire\WithPagination;

class EmployeDetails extends Component
{
    use WithPagination;

    public $employeId;
    public $employe;
    public $showInfoEdit = true;
    public $showModal = false;
    public $showGestM = false;

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public $historique_gestionnaire;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'closeEditModal' => 'closeModal',
        'closeGestModal' => 'closeGestModal',
        'employeUpdated' => 'handleEmployeUpdated',
        'gestionnaireAjoute' => 'gestionnaireAjoute'
    ];



    public function mount()
    {
        $this->employe =  Employe::with('gestionnaire')->findOrFail($this->employeId);
    }

    public function rules()
    {
        return [
            'current_password' => ['required'],
            'new_password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->symbols()
            ],
            'new_password_confirmation' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'new_password.required' => 'Le nouveau mot de passe est obligatoire.',
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins :min caractères.',
            'new_password.letters' => 'Le nouveau mot de passe doit contenir au moins une lettre.',
            'new_password.numbers' => 'Le nouveau mot de passe doit contenir au moins un chiffre.',
            'new_password.mixed' => 'Le nouveau mot de passe doit contenir des majuscules et des minuscules.',
            'new_password.symbols' => 'Le nouveau mot de passe doit contenir au moins un caractère spécial.',
            'new_password_confirmation.required' => 'La confirmation du mot de passe est obligatoire.'
        ];
    }

    public function toogle_info()
    {
        $this->showInfoEdit = true;
    }

    public function toogle_pwd()
    {
        $this->showInfoEdit = false;
    }

    public function showEditModal()
    {
        $this->showModal = !$this->showModal;
    }

    public function closeModal($val = null)
    {
        $val ? $this->showModal = $this->val : $this->showModal = !$this->showModal;
    }

    public function showGestModal()
    {
        $this->showGestM = !$this->showGestM;
    }


    public function closeGestModal($val = null)
    {
        $val ? $this->showGestM = $this->val : $this->showGestM = !$this->showGestM;
    }


    public function handleEmployeUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Les informations de l\'employé ont été modifiés avec succès.');
    }

    public function gestionnaireAjoute()
    {
        $this->closeGestModal();
        session()->flash('success', 'Le nouveau gestionaire a été ajouté avec succès.');
    }

    public function changePassword()
    {
        $this->validate();

        $user = User::findOrFail($this->employe->user_id);
        if (!Hash::check($this->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => __('L\'ancien mot de passe est incorrect.'),
            ]);
        }
        $user->update([
            'password' => $this->new_password
        ]);

        session()->flash('success', 'Mot de passe mis à jour avec succès.');

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function get_historique_gestionnaire()
    {
        return HistoriqueGestionnaire::with('gestionnaire')->where('employe_id', $this->employeId)->paginate(10);
    }

    public function render()
    {

        return view('rhemploye::livewire.employe-details', ['gestionnaire_historique' => $this->get_historique_gestionnaire()]);
    }
}
