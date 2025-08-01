<?php

namespace Modules\Rh\Livewire\Employe;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Rh\Models\Employe\HistoriqueHeuresSemaines;

class HistoriqueHeuresSemainesForm extends Component
{
    public $employeId;
    public $heure;
    public $dateDebut;

    public function mount()
    {
        $this->dateDebut = Carbon::now()->format('Y-m-d\TH:i');
    }

    //--- Règles de validation pour l'ajout/modification de l'historique de gestionnaire d'un employe
    public function rules()
    {
        return [
            'heure' => [
                'required',
                'numeric'
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
            "heure.required" => "L'heure est obligatoire.",
            "heure.numeric" => "Le format de l'a.",
            "dateDebut.required" => "La date de début est obligatoire.",
            "dateDebut.date" => "La date de début doit être une date valide.",
        ];
    }

    public function saveHist()
    {
        $this->validate();
        $user_connect = Auth::user();

        try {
            $dernierHistorique = HistoriqueHeuresSemaines::orderBy('id', 'desc')->first();

            if ($dernierHistorique) {
                $dernierHistorique->update([
                    'date_fin' => $this->dateDebut,
                ]);
            }

            $hist = HistoriqueHeuresSemaines::create([
                'employe_id' => $this->employeId,
                'nombre_d_heure_semaine' => $this->heure,
                'date_debut' => $this->dateDebut,
            ]);

            //--- Journalisation
            Log::channel('daily')->info("L'heure de la semaine de l'employé " . $hist->employe?->nom . " " . $hist->employe?->prenom . " vient d'être modifiée par l' utilisateur " . $user_connect->name);

            $this->reset(['heure', 'dateDebut']);
            $this->dispatch('HeureAjoute');
        } catch (\Throwable $th) {

            //--- Journalisation
            Log::channel('daily')->error(
                "Erreur lors de la modification de l'heure de la semaine de l'employé " . $hist->employe?->nom . " " . $hist->employe?->prenom . " par l' utilisateur " . $user_connect->name,
                ['message' => $th->getMessage()]
            );

            session()->flash('error', 'Erreur lors de l’enregistrement : ' . $th->getMessage());
        }
    }

    public function cancel()
    {
        $this->heure = "";
        $this->dateDebut = Carbon::now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('rh::livewire.employe.historique-heures-semaines-form');
    }
}
