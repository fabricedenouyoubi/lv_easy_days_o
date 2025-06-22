<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class GroupPermission extends Component
{
    use WithPagination;

    public $groupId;
    public $name_searched;
    public $code_searched;
    public $type_searched;
    public $checkedPermissions = [];
    public $role;

    protected $paginationTheme = 'bootstrap';

    //---  recuperation des permissions d'un groupe
    public function get_group_permission()
    {
        $group = Role::query()->with('permissions')->where('id', $this->groupId)->first();
        //return $group->permissions()->pluck('permission_id')->toArray();
        return $group->getPermissionNames();
    }

    //---  recuperation de toutes les permissions
    public function get_all_permission()
    {
        return Permission::query()->pluck('name')->toArray();
    }

    /*
        - operation au montage du composant des permisions d'un groupe
        - chargement des permission d'un groupe
    */
    public function mount()
    {
        $this->checkedPermissions = $this->get_group_permission();
        $this->role = Role::query()->where('id', $this->groupId)->first();
    }

    //--- fonction de selection de toutes les permissions pour un groupe
    public function select_all()
    {
        $this->checkedPermissions = $this->get_all_permission();
    }

    //--- fonction de selection de toutes les permissions pour un groupe;
    public function deselect_all()
    {
        $this->checkedPermissions = [];
    }

    //--- fontion de modification des permissions d'un utilisateur
    public function set_group_permission()
    {
        try {
            //--- mise a jour des permission du groupe
            $group = Role::query()->with('permissions')->where('id', $this->groupId)->first();
            $group->syncPermissions($this->checkedPermissions);
            $this->dispatch('groupPermissionUpdated', $group->name);
        } catch (\Throwable $th) {
            dd($th->getMessage());
            $this->addError('error', 'Erreur de sauvegarde ' . $th->getMessage());
        }
    }

    //--- fonction de recuperation de toutes les permissions avec pagination et recherche
    public function get_permission()
    {
        return Permission::query()
            ->when(
                $this->name_searched,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->name_searched . '%')
            )
            ->when(
                $this->type_searched,
                fn($query) =>
                $query->where('module', 'like', '%' . $this->type_searched . '%')
            )->paginate(10, ['*'], 'permission');
    }

    //--- fonction reinitialisation des champs de filtre des permissions
    public function resetFilters()
    {
        $this->reset(['name_searched', 'type_searched']);
    }

    public function get_permission_groups()
    {
        return Permission::orderBy('module')->get()->groupBy('module');
    }

    public function render()
    {
        return view('livewire.group-permission', ['permissions' => $this->get_permission(), 'permissionGroups' => $this->get_permission_groups()]);
    }
}
