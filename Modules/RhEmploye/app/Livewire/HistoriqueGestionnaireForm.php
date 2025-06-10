<?php

namespace Modules\RhEmploye\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Modules\RhEmploye\Models\Employe;
use Modules\RhEmploye\Models\HistoriqueGestionnaire;

class HistoriqueGestionnaireForm extends Component
{
    public $gestionnaire_list;
    public $employeId;
    public $gestionnaire;
    public $dateDebut;


    public function mount()
    {
        try {
            $employe_user_id = Employe::findOrFail($this->employeId)->user_id;
            $gestionnaire_group_id = Group::where('name', 'GESTIONNAIRE')->first()->id;
            $this->gestionnaire_list = User::with('groups')->whereHas('groups', function ($query) use ($gestionnaire_group_id, $employe_user_id) {
                $query->where('group_id', $gestionnaire_group_id);
                $query->where('user_id', '!=', $employe_user_id);
            })->orderBy('name', 'asc')->get();
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la selection des gestionnaires : ' . $th->getMessage());
        }

        $this->dateDebut = Carbon::now()->format('Y-m-d\TH:i');
    }


    public function rules()
    {
        return [
            'gestionnaire' => [
                'required',
                'integer'
            ],
            'dateDebut' => [
                'required',
                'date',
            ],
        ];
    }

    public function messages()
    {
        return [
            'gestionnaire.required' => 'Le gestionnaire est obligatoire.',
            'gestionnaire.integer' => 'Le gestionnaire est obligatoire.',
            'dateDebut.required' => 'La date de début est obligatoire.',
            'dateDebut.date' => 'La date de début doit être une date valide.',
        ];
    }

    public function saveHist()
    {
        $this->validate();

        try {
            $employe = Employe::findOrFail($this->employeId);

            $employe->update([
                'gestionnaire_id' => $this->gestionnaire,
            ]);

            HistoriqueGestionnaire::create([
                'employe_id' => $this->employeId,
                'gestionnaire_id' => $this->gestionnaire,
                'date_debut' => $this->dateDebut,
            ]);

            $this->reset(['gestionnaire', 'dateDebut']);
            $this->dispatch('gestionnaireAjoute');
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de l’enregistrement : ' . $th->getMessage());
        }
    }

    public function cancel()
    {
        $this->dispatch('closeGestModal', false);
    }

    public function render()
    {
        return view('rhemploye::livewire.historique-gestionnaire-form');
    }
}
