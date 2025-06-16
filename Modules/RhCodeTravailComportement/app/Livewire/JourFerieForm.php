<?php

namespace Modules\RhCodeTravailComportement\Livewire;

use Livewire\Component;
use Modules\RhCodeTravailComportement\Models\Configuration;
use Carbon\Carbon;
use Modules\Budget\Models\AnneeFinanciere;

class JourFerieForm extends Component
{
    public $jourFerieId;
    public $codeTravailId;
    public $libelle;
    public $date;
    public $commentaire;

    protected $listeners = ['editJourFerie'];

    protected function rules()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        $minDate = $anneeBudgetaire ? $anneeBudgetaire->debut->format('Y-m-d') : now()->format('Y-m-d');
        $maxDate = $anneeBudgetaire ? $anneeBudgetaire->fin->format('Y-m-d') : now()->addYear()->format('Y-m-d');
        
        return [
            'libelle' => 'required|string|max:200',
            'date' => [
                'required',
                'date',
                'after_or_equal:' . $minDate,
                'before_or_equal:' . $maxDate,
                'unique:configurations,date,NULL,id,code_travail_id,' . $this->codeTravailId . ',employe_id,NULL'
            ],
            'commentaire' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'libelle.required' => 'Le libellé est obligatoire.',
        'libelle.max' => 'Le libellé ne peut pas dépasser 200 caractères.',
        'date.required' => 'La date est obligatoire.',
        'date.date' => 'La date doit être valide.',
        'date.after_or_equal' => 'La date doit être dans l\'année budgétaire active.',
        'date.before_or_equal' => 'La date doit être dans l\'année budgétaire active.',
        'date.unique' => 'Un jour férié existe déjà à cette date pour ce code de travail.',
        'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
    ];

    public function mount($jourFerieId = null, $codeTravailId = null)
    {
        $this->jourFerieId = $jourFerieId;
        $this->codeTravailId = $codeTravailId;
        
        if ($jourFerieId) {
            $this->loadJourFerie();
        }
    }

    public function editJourFerie($jourFerieId)
    {
        $this->jourFerieId = $jourFerieId;
        $this->loadJourFerie();
    }

    private function loadJourFerie()
    {
        if ($this->jourFerieId) {
            $jourFerie = Configuration::findOrFail($this->jourFerieId);
            
            $this->libelle = $jourFerie->libelle;
            $this->date = $jourFerie->date->format('Y-m-d');
            $this->commentaire = $jourFerie->commentaire;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                session()->flash('error', 'Aucune année budgétaire active trouvée.');
                return;
            }

            $data = [
                'libelle' => $this->libelle,
                'date' => $this->date,
                'commentaire' => $this->commentaire,
                'quota' => 0,
                'consomme' => 0,
                'reste' => 0,
                'employe_id' => null, 
                'annee_budgetaire_id' => $anneeBudgetaire->id,
                'code_travail_id' => $this->codeTravailId,
            ];

            if ($this->jourFerieId) {
                // Modification
                $jourFerie = Configuration::findOrFail($this->jourFerieId);
                $jourFerie->update($data);
                
                $this->dispatch('jourFerieUpdated');
            } else {
                // Création
                Configuration::create($data);
                
                $this->dispatch('jourFerieCreated');
            }

            $this->reset(['libelle', 'date', 'commentaire']);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset(['libelle', 'date', 'commentaire']);
        $this->dispatch('modalClosed');
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function render()
    {
        return view('rhcodetravailcomportement::livewire.jour-ferie-form', [
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive
        ]);
    }
}