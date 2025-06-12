<?php

namespace App\Livewire;

use App\Models\Permission as ModelsPermission;
use Livewire\Component;
use Livewire\WithPagination;

class Permission extends Component
{
    use WithPagination;

    public $name_searched;
    public $code_searched;
    public $type_searched;

    protected $paginationTheme = 'bootstrap';

    public function get_permission()
    {
        return ModelsPermission::query()
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
            })->paginate(10);
    }

    public function resetFilter()
    {
        $this->reset(['name_searched', 'code_searched']);
    }

    public function render()
    {
        return view('livewire.permission', [
            'permissions' => $this->get_permission()
        ]);
    }
}
