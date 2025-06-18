<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission as ModelsPermission;

class Permission extends Component
{
    use WithPagination;

    public $name_searched;
    public $type_searched;

    protected $paginationTheme = 'bootstrap';

    //--- fonction de recuperation de toutes les permissions avec pagination et recherche
    public function get_permission()
    {
        return ModelsPermission::query()
            ->when(
                $this->name_searched,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->name_searched . '%')
            )
            ->when(
                $this->type_searched,
                fn($query) =>
                $query->where('module', 'like', '%' . $this->type_searched . '%')
            )
            ->paginate(10);
    }

    //--- fonction reinitialisation des champs de filtre des permissions
    public function resetFilters()
    {
        $this->reset(['name_searched', 'type_searched']);
    }

    public function render()
    {
        return view('livewire.permission', [
            'permissions' => $this->get_permission()
        ]);
    }
}
