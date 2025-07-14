<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire;

use Livewire\Component;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

class CodeTravailForm extends Component
{
    public $codeTravailId;
    public $code;
    public $libelle;
    public $categorie_id;
    public $estAjustable = true;
    public $estBanque = false;
    public $cumuleBanque = false;

    protected $listeners = ['editCodeTravail'];

    protected function rules()
    {
        $rules = [
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:codes_travail,code'
            ],
            'libelle' => 'required|string|max:150',
            'categorie_id' => 'required|exists:categories,id',
            'estAjustable' => 'boolean',
            'estBanque' => 'boolean',
            'cumuleBanque' => 'boolean',
        ];

        // Si on modifie, exclure l'ID actuel de la validation d'unicité
        if ($this->codeTravailId) {
            $rules['code'] = [
                'required',
                'string',
                'max:20',
                'unique:codes_travail,code,' . $this->codeTravailId
            ];
        }

        return $rules;
    }

    protected $messages = [
        'code.required' => 'Le code est obligatoire.',
        'code.unique' => 'Ce code existe déjà.',
        'code.max' => 'Le code ne peut pas dépasser 20 caractères.',
        'libelle.required' => 'Le libellé est obligatoire.',
        'libelle.max' => 'Le libellé ne peut pas dépasser 150 caractères.',
        'categorie_id.required' => 'La catégorie est obligatoire.',
        'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
    ];

    public function mount($codeTravailId = null)
    {
        $this->codeTravailId = $codeTravailId;

        if ($codeTravailId) {
            $this->loadCodeTravail();
        }
    }

    public function editCodeTravail($codeTravailId)
    {
        $this->codeTravailId = $codeTravailId;
        $this->loadCodeTravail();
    }

    private function loadCodeTravail()
    {
        if ($this->codeTravailId) {
            $codeTravail = CodeTravail::with('categorie')->findOrFail($this->codeTravailId);

            $this->code = $codeTravail->code;
            $this->libelle = $codeTravail->libelle;
            $this->categorie_id = $codeTravail->categorie_id;
            $this->estAjustable = $codeTravail->est_ajustable;
            $this->estBanque = $codeTravail->est_banque;
            $this->cumuleBanque = $codeTravail->cumule_banque;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'code' => strtoupper($this->code),
                'libelle' => $this->libelle,
                'categorie_id' => $this->categorie_id,
                'est_ajustable' => $this->estAjustable,
                'est_banque' => $this->estBanque,
                'cumule_banque' => $this->cumuleBanque,
            ];

            if ($this->codeTravailId) {
                // Modification
                $codeTravail = CodeTravail::findOrFail($this->codeTravailId);
                $codeTravail->update($data);

                $this->dispatch('codeTravailUpdated');
            } else {
                // Création
                CodeTravail::create($data);

                $this->dispatch('codeTravailCreated');
            }

            $this->reset();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset();
        $this->dispatch('modalClosed');
    }

    public function getCategoriesProperty()
    {
        return Categorie::orderBy('intitule')->get();
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.code-travail-form', [
            'categories' => $this->categories
        ]);
    }
}
