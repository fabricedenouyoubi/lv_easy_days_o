<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Collectif;

use Livewire\Component;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

class CollectifList extends Component
{
    public $codeTravailId;
    public $codeTravail;
    public $quotaTotal = '';
    public $isEditing = false;
    public $showAffectation = false;
    public $configuration = null;

    protected $listeners = [
        'employesAffected' => 'handleEmployesAffected',
        'refreshComponent' => '$refresh'
    ];

    protected function rules()
    {
        return [
            'quotaTotal' => 'required|numeric|min:0|max:9999.99',
        ];
    }

    protected $messages = [
        'quotaTotal.required' => 'Le quota total est obligatoire.',
        'quotaTotal.numeric' => 'Le quota total doit être un nombre.',
        'quotaTotal.min' => 'Le quota total doit être positif.',
        'quotaTotal.max' => 'Le quota total ne peut pas dépasser 9999.99.',
    ];

    public function mount($codeTravailId)
    {
        $this->codeTravailId = $codeTravailId;
        $this->codeTravail = CodeTravail::with('categorie')->findOrFail($codeTravailId);
        $this->loadConfiguration();
    }

    public function loadConfiguration()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if ($anneeBudgetaire) {
            $this->configuration = Configuration::collectif()
                ->where('code_travail_id', $this->codeTravailId)
                ->where('annee_budgetaire_id', $anneeBudgetaire->id)
                ->with(['employes'])
                ->first();
            
            if ($this->configuration) {
                $this->quotaTotal = $this->configuration->quota;
            }
        }
    }

    public function enableEditing()
    {
        $this->isEditing = true;
    }

    public function saveQuota()
    {
        $this->validate();

        try {
            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                session()->flash('error', 'Aucune année financière active trouvée.');
                return;
            }

            if ($this->configuration) {
                // Mise à jour du quota existant
                $totalConsomme = $this->configuration->employes->sum('pivot.consomme_individuel');
                
                $this->configuration->update([
                    'quota' => $this->quotaTotal,
                    'reste' => max(0, $this->quotaTotal - $totalConsomme)
                ]);
                
                session()->flash('success', 'Quota collectif modifié avec succès.');
            } else {
                // Création d'une nouvelle configuration
                $this->configuration = Configuration::create([
                    'libelle' => 'Configuration collective - ' . $this->codeTravail->libelle,
                    'quota' => $this->quotaTotal,
                    'consomme' => 0,
                    'reste' => $this->quotaTotal,
                    'date' => null,
                    'commentaire' => '',
                    'employe_id' => null,
                    'annee_budgetaire_id' => $anneeBudgetaire->id,
                    'code_travail_id' => $this->codeTravailId,
                ]);
                
                session()->flash('success', 'Quota collectif créé avec succès.');
            }

            $this->isEditing = false;
            $this->loadConfiguration();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        if ($this->configuration) {
            $this->quotaTotal = $this->configuration->quota;
        } else {
            $this->quotaTotal = '';
        }
    }

    public function showAffectationModal()
    {
        if (!$this->configuration) {
            session()->flash('error', 'Veuillez d\'abord définir le quota total.');
            return;
        }
        
        $this->showAffectation = true;
    }

    public function closeAffectationModal()
    {
        $this->showAffectation = false;
    }

    public function handleEmployesAffected()
    {
        $this->closeAffectationModal();
        $this->loadConfiguration();
        session()->flash('success', 'Employés affectés avec succès.');
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function getEmployesAffectesProperty()
    {
        return $this->configuration ? $this->configuration->employes : collect();
    }

    public function getQuotaConsommeProperty()
    {
        return $this->employesAffectes->sum('pivot.consomme_individuel');
    }

    public function getQuotaRestantProperty()
    {
        if (!$this->configuration) {
            return 0;
        }
        return max(0, $this->configuration->quota - $this->quotaConsomme);
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.collectif.collectif-list', [
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive,
            'employesAffectes' => $this->employesAffectes,
            'quotaConsomme' => $this->quotaConsomme,
            'quotaRestant' => $this->quotaRestant
        ]);
    }
}