<?php

namespace Modules\Budget\Livewire;

use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Carbon\Carbon;

class AnneeFinanciereForm extends Component
{
    public $anneeId;
    public $debut;
    public $fin;
    public $statut = 'ACTIF';
    public $actif = true;

    protected $listeners = ['editAnnee'];

    protected function rules()
    {
        return [
            'debut' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (!AnneeFinanciere::isValideDateDebut($value)) {
                        $fail('La date de début doit être le 1er avril.');
                    }
                },
                function ($attribute, $value, $fail) {
                    if ($this->fin && AnneeFinanciere::hasDateOverlap($value, $this->fin, $this->anneeId)) {
                        $fail('Cette période chevauche avec une année financière existante.');
                    }
                }
            ],
            'fin' => [
                'required',
                'date',
                'after:debut',
                function ($attribute, $value, $fail) {
                    if ($this->debut && !AnneeFinanciere::isValideDateFin($this->debut, $value)) {
                        $fail('La date de fin doit être le 31 mars de l\'année suivante.');
                    }
                }
            ],
            'statut' => 'required|in:ACTIF,INACTIF',
            'actif' => 'boolean'
        ];
    }

    protected $messages = [
        'debut.required' => 'La date de début est obligatoire.',
        'debut.date' => 'La date de début doit être une date valide.',
        'fin.required' => 'La date de fin est obligatoire.',
        'fin.date' => 'La date de fin doit être une date valide.',
        'fin.after' => 'La date de fin doit être postérieure à la date de début.',
        'statut.required' => 'Le statut est obligatoire.',
        'statut.in' => 'Le statut doit être ACTIF ou INACTIF.',
    ];

    public function mount($anneeId = null)
    {
        $this->anneeId = $anneeId;
        
        if ($anneeId) {
            $this->loadAnnee();
        } else {
            // Valeurs par defaut pour une nouvelle année
            $currentYear = now()->year;
            $this->debut = Carbon::create($currentYear, 4, 1)->format('Y-m-d');
            $this->fin = Carbon::create($currentYear + 1, 3, 31)->format('Y-m-d');
        }
    }

    public function editAnnee($anneeId)
    {
        $this->anneeId = $anneeId;
        $this->loadAnnee();
    }

    private function loadAnnee()
    {
        if ($this->anneeId) {
            $annee = AnneeFinanciere::findOrFail($this->anneeId);
            $this->debut = $annee->debut->format('Y-m-d');
            $this->fin = $annee->fin->format('Y-m-d');
            $this->statut = $annee->statut;
            $this->actif = $annee->actif;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->anneeId) {
                // Modification
                $annee = AnneeFinanciere::findOrFail($this->anneeId);
                $annee->update([
                    'debut' => $this->debut,
                    'fin' => $this->fin,
                    'statut' => $this->statut,
                    'actif' => $this->actif,
                ]);
                
                $this->dispatch('anneeFinanciereUpdated');
            } else {
                // Création
                AnneeFinanciere::create([
                    'debut' => $this->debut,
                    'fin' => $this->fin,
                    'statut' => $this->statut,
                    'actif' => $this->actif,
                ]);
                
                $this->dispatch('anneeFinanciereCreated');
            }

            $this->reset(['debut', 'fin', 'statut', 'actif', 'anneeId']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['debut', 'fin', 'statut', 'actif', 'anneeId']);
        $this->dispatch('modalClosed');
    }
    public function render()
    {
        return view('budget::livewire.annee-financiere-form');
    }
}
