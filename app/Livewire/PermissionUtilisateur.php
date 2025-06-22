<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

class PermissionUtilisateur extends Component
{
    use WithPagination;
    public $userId;
    public $userName;
    public $name_searched;
    public $type_searched;
    public $checkedPermissions = [];

    protected $paginationTheme = 'bootstrap';

    //---  recuperation des permissions d'un utilisateur
    /* public function get_user_permission()
    {
        $user = User::query()->with('permissions')->where('id', $this->userId)->first();
        return $user->permissions()->pluck('permission_id')->toArray();
    } */

    //---  recuperation de toutes les permissions
    /* public function get_all_permission()
    {
        return Permission::query()->pluck('id')->toArray();
    } */

    /*
        - operation au montage du composant des permisions d'un utilisateur
        - chargement des permission d'un utilisateur
    */
    /* public function mount()
    {
        $this->checkedPermissions = $this->get_user_permission();
    } */

    /*     public function hide_user_modal()
    {
        $this->resetPage('permission');
        $this->dispatch('resetPageNumber');
    } */

    //--- fonction de selection de toutes les permissions pour un utilisateur
    /* public function select_all()
    {
        $this->checkedPermissions = $this->get_all_permission();
    } */

    //--- fonction de selection de toutes les permissions pour un utilisateur
    /* public function deselect_all()
    {
        $this->checkedPermissions = [];
    } */

    //--- fontion de modification des permissions d'un utilisateur
    /* public function set_user_permission()
    {
        try {
            $user = User::query()->with('permissions')->where('id', $this->userId)->first();
            $user->permissions()->sync($this->checkedPermissions);
            $this->dispatch('userPermissionUpdated', $user->name);
        } catch (\Throwable $th) {
            $this->addError('error', 'Erreur de sauvegarde ' . $th->getMessage());
        }
    } */

    //--- fonction de recuperation de toutes les permissions avec pagination et recherche
    public function get_permission()
    {
        $user = User::findOrFail($this->userId);

        // Obtenir toutes les permissions via les rôles de l'utilisateur (avec Eloquent)
        return $permissions = Permission::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })
            ->when($this->name_searched, function ($query) {
                $query->where('name', 'like', '%' . $this->name_searched . '%');
            })
            ->when($this->type_searched, function ($query) {
                $query->where('module', 'like', '%' . $this->type_searched . '%');
            })
            ->paginate(10, ['*'], 'permission');
    }

    //--- fonction reinitialisation des champs de filtre des permissions
    public function resetFilters()
    {
        $this->reset(['name_searched', 'type_searched']);
    }

    public function get_permission_groups()
    {
        $user = User::findOrFail($this->userId);
        return Permission::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->orderBy('module')->get()->groupBy('module');
    }

    public function render()
    {
        //dd($this->checkedPermissions);
        return view(
            'livewire.permission-utilisateur',
            [
                'permissions' => $this->get_permission(),
                'permissionGroups' => $this->get_permission_groups()
            ]
        );
    }
}
