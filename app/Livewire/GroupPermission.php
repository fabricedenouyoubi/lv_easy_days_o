<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\Permission;
use Livewire\Component;
use Livewire\WithPagination;

class GroupPermission extends Component
{
    use WithPagination;

    public $groupId;
    public $name_searched;
    public $code_searched;
    public $type_searched;
    public $checkedPermissions = [];

    protected $paginationTheme = 'bootstrap';

    //---  recuperation des permissions d'un groupe
    public function get_group_permission()
    {
        $group = Group::query()->with('permissions')->where('id', $this->groupId)->first();
        return $group->permissions()->pluck('permission_id')->toArray();
    }

    //---  recuperation de toutes les permissions
    public function get_all_permission()
    {
        return Permission::query()->pluck('id')->toArray();
    }

    /*
        - operation au montage du composant des permisions d'un groupe
        - chargement des permission d'un groupe
    */
    public function mount()
    {
        $this->checkedPermissions = $this->get_group_permission();
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
            $group = Group::query()->with('permissions')->where('id', $this->groupId)->first();
            $group->permissions()->sync($this->checkedPermissions);

            //--- mise a jour des permission des utilisateur group

            $users = $group->users; //--- selection des utilisateurs du groupe

            foreach ($users as $user) {
                $goupes = $user->groups; //--- selection des groupes de l'utilisateur

                foreach ($goupes as $groupe) {
                    $user->permissions()->sync($groupe->permissions->pluck('id')->toArray()); //--- mise a jour des permission de l'utilisateur
                }
            }

            $this->dispatch('groupPermissionUpdated', $group->name);
        } catch (\Throwable $th) {
            $this->addError('error', 'Erreur de sauvegarde ' . $th->getMessage());
        }
    }

    //--- fonction de recuperation de toutes les permissions avec pagination et recherche
    public function get_permission()
    {
        return Permission::query()
            ->with('contentType')
            ->when(
                $this->name_searched,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->name_searched . '%')
            )
            ->when(
                $this->code_searched,
                fn($query) =>
                $query->where('codename', 'like', '%' . $this->code_searched . '%')
            )
            ->when($this->type_searched, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereHas('contentType', function ($Query) {
                        $Query->where('app_label', 'like', '%' . $this->type_searched . '%');
                    });
                });
            })->paginate(10, ['*'], 'permission');
    }

    //--- fonction reinitialisation des champs de filtre des permissions
    public function resetFilter()
    {
        $this->reset(['name_searched', 'code_searched', 'type_searched']);
    }


    public function render()
    {
        return view('livewire.group-permission', ['permissions' => $this->get_permission()]);
    }
}
