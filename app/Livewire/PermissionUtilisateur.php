<?php

namespace App\Livewire;

use App\Models\Permission;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PermissionUtilisateur extends Component
{
    use WithPagination;
    public $userId;
    public $name_searched;
    public $code_searched;
    public $type_searched;
    public $checkedPermissions = [];

    protected $paginationTheme = 'bootstrap';

    public function get_user_permission()
    {
        $user = User::query()->with('permissions')->where('id', $this->userId)->first();
        return $user->permissions()->pluck('permission_id')->toArray();
    }

    public function get_all_permission()
    {
        return Permission::query()->pluck('id')->toArray();
    }

    public function mount()
    {
        $this->checkedPermissions = $this->get_user_permission();
    }

    public function select_all()
    {
        $this->checkedPermissions = $this->get_all_permission();
    }

    public function deselect_all()
    {
        $this->checkedPermissions = [];
    }

    public function set_user_permission()
    {

        dd($this->checkedPermissions);
        try {
            $user = User::query()->with('permissions')->where('id', $this->userId)->first();
            $user->permissions->sync($this->checkedPermissions);
            $this->dispatch('userPermissionUpdated');
        } catch (\Throwable $th) {
            $this->addError('error', 'Erreur de sauvegarde ' . $th->getMessage());
        }
    }

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

    public function resetFilter()
    {
        $this->reset(['name_searched', 'code_searched', 'type_searched']);
    }

    public function render()
    {
        //dd($this->checkedPermissions);
        return view(
            'livewire.permission-utilisateur',
            [
                'permissions' => $this->get_permission(),
            ]
        );
    }
}
