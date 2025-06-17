<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class GestionUtilisateur extends Component
{
    use WithPagination;

    public $email_searched;
    public $name_searched;
    public $groupe_searched;
    public $userName;
    public $userPermissionModal = false;
    public $userId;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'userPermissionUpdated' => 'handleUserPermissionUpdated',
        //'resetPageNumber' => 'handleresetPageNumber',
    ];

    //--- fonction reinitialisation des champs de filtre des utilisateur
    public function resetFilter()
    {
        $this->reset(['email_searched', 'name_searched', 'groupe_searched']);
    }

    //--- affichage du tableau des permissions d'un uilisateur
    public function show_user_modal($name, $id)
    {
        $this->userName = $name;
        $this->userId = $id;
        $this->userPermissionModal = true;
    }

    //--- fermuture du tabaleau des permissions d'un uilisateur
    public function hide_user_modal()
    {
        $this->userPermissionModal = !$this->userPermissionModal;
    }

    /*     public function handleresetPageNumber()
    {
        $this->userPermissionModal = !$this->userPermissionModal;
    } */

    //--- recuperation de la liste des utilisateurs
    public function get_utilisateurs()
    {
        return User::with('groups')
            ->when(
                $this->email_searched,
                fn($query) =>
                $query->where('email', 'like', '%' . $this->email_searched . '%')
            )
            ->when(
                $this->name_searched,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->name_searched . '%')
            )
            ->when($this->groupe_searched, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('groups', function ($Query) {
                        $Query->where('name', 'like', '%' . $this->groupe_searched . '%');
                    });
                });
            })->orderBy('name', 'asc')
            ->paginate(10);
    }

    //--- affichage du message de mis a jour des permission d'un utilisateur
    public function handleUserPermissionUpdated($val = null)
    {
        $this->hide_user_modal();
        session()->flash('success', 'Permissions : ' . $val . ' mises à jour avec succès.');
    }

    public function render()
    {
        //dd($this->get_utilisateurs());
        return view('livewire.gestion-utilisateur', ['utilisateurs' => $this->get_utilisateurs()]);
    }
}
