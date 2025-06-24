<?php

namespace Modules\Rh\Livewire\Employe;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Modules\Rh\Models\Employe\Employe;
use Modules\Rh\Models\Employe\HistoriqueGestionnaire;
use Spatie\Permission\Models\Role;

class HistoriqueGestionnaireForm extends Component
{
    public $gestionnaire_list;
    public $employeId;
    public $gestionnaire;
    public $dateDebut;

    /*
        - operation au montage du composant de l'historique de gestionnaire d'un employe
        - chargement des gestionnaires
    */
    public function mount()
    {
        try {
            $this->gestionnaire_list = Employe::where('id', '!=', $this->employeId)->where('est_gestionnaire', true)->get();
        } catch (\Throwable $th) {
            session()->flash('error', 'Erreur lors de la selection des gestionnaires : ' . $th->getMessage());
        }

        $this->dateDebut = Carbon::now()->format('Y-m-d\TH:i');
    }

    //--- Règles de validation pour l'ajout de l'historique de gestionnaire d'un employe
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

    //--- Messages de validation pour l'ajout de l'historique de gestionnaire d'un employe
    public function messages()
    {
        return [
            'gestionnaire.required' => 'Le gestionnaire est obligatoire.',
            'gestionnaire.integer' => 'Le gestionnaire est obligatoire.',
            'dateDebut.required' => 'La date de début est obligatoire.',
            'dateDebut.date' => 'La date de début doit être une date valide.',
        ];
    }

    //---fonction d'ajout de l'historique de gestionnaire d'un employe
    public function saveHist()
    {
        $this->validate();

        try {
            $employe = Employe::findOrFail($this->employeId);

            $employe->update([
                'gestionnaire_id' => $this->gestionnaire,
            ]);

            $dernierHistorique = HistoriqueGestionnaire::orderBy('id', 'desc')->first();

            if ($dernierHistorique) {
                $dernierHistorique->update([
                    'date_fin' => $this->dateDebut,
                ]);
            }

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

    //--- fermeture du formulaire de l'historique de gestionnaire d'un employe
    public function cancel()
    {
        $this->gestionnaire = "";
        $this->dateDebut = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('rh::livewire.employe.historique-gestionnaire-form');
    }
}
