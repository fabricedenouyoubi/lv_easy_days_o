<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Groupe extends Component
{
    use WithPagination;

    public $name_searched;
    public $show_add_group = false;
    public $groupId;
    public $groupName;

    public $group_permission = false;

    protected $paginationTheme = 'bootstrap';

    //--- ecouteur d'evenement venamt des composants enfants [groupe-form]
    protected $listeners = [
        'closeModal' => 'hide_groupe_add_modal',
        'groupCreated' => 'handlegroupCreated',
        'groupUpdated' => 'handlegroupUpdated',
        'groupPermissionUpdated' => 'handleGroupPermissionUpdated',
    ];

    //--- recupeartion de la liste des goupes
    public function get_groupes()
    {
        return Role::query()
            ->when(
                $this->name_searched,
                fn($query) =>
                $query->where('name', 'like', '%' . $this->name_searched . '%')
            )
            ->orderBy('name', 'asc')
            ->paginate(10);
    }

    //--- afficher le formualaire d'ajout d'un groupe
    public function show_add_group_modal()
    {
        $this->groupId = null;
        $this->show_add_group = true;
    }

    //--- fermer le formualaire d'ajout et de moficiation d'un groupe
    public function hide_groupe_add_modal($val = null)
    {
        $val ?: $this->show_add_group = $val;
        $this->show_add_group = false;
    }

    //--- afficher le formualaire de moficiation d'un groupe
    public function show_edit_groupe_modal($groupeId)
    {
        $this->groupId = $groupeId;
        $this->show_add_group = true;
    }

    //--- afficher le tableau de permission d'un groupe
    public function show_group_permission_modal($id, $name)
    {
        $this->groupId = $id;
        $this->groupName = $name;
        $this->group_permission = true;
    }

    //--- fermer le tableau de permission d'un groupe
    public function hide_group_permission_modal()
    {
        $this->reset('groupId', 'groupName', 'group_permission');
    }

    //--- fonction d'affichage du message de creation d'un group
    public function handlegroupCreated()
    {
        $this->hide_groupe_add_modal();
        session()->flash('success', 'Groupe crée avec succès.');
    }

    //--- fonction d'affichage du message de modification d'un employe
    public function handlegroupUpdated()
    {
        $this->hide_groupe_add_modal();
        session()->flash('success', 'Groupe modifié avec succès.');
    }

    public function handleGroupPermissionUpdated($val = null)
    {
        $this->hide_group_permission_modal();
        session()->flash('success', 'Permissions du groupe : ' . $val . ' mises à jour avec succès.');
    }

    //--- reinitialisation des champs de recherche d'un goupe
    public function resetFilter()
    {
        $this->reset('name_searched');
    }

    public function render()
    {
        return view('livewire.groupe', ['groups' => $this->get_groupes()]);
    }
}
