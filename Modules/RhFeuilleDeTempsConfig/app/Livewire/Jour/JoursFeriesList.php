<?php

namespace Modules\RhFeuilleDeTempsConfig\Livewire\Jour;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;
use Modules\RhFeuilleDeTempsConfig\Services\HolidayGeneratorService;
use Illuminate\Support\Facades\Log;

class JoursFeriesList extends Component
{
    use WithPagination;

    public $codeTravailId;
    public $codeTravail;
    public $searchLibelle = '';
    public $isFiltering = false;
    public $showModal = false;
    public $editingId = null;
    public $showDetail = false;
    public $detailJourFerieId = null;
    
    // Propriétés pour la génération simple
    public $isGenerating = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'jourFerieCreated' => 'handleJourFerieCreated',
        'jourFerieUpdated' => 'handleJourFerieUpdated',
        'refreshComponent' => '$refresh'
    ];

    public function mount($codeTravailId)
    {
        $this->codeTravailId = $codeTravailId;
        $this->codeTravail = CodeTravail::with('categorie')->findOrFail($codeTravailId);
        
        Log::info('JoursFeriesList monté avec code travail', [
            'codeTravailId' => $codeTravailId,
            'codeTravail' => $this->codeTravail->libelle
        ]);
    }

    public function updatingSearchLibelle()
    {
        $this->resetPage();
    }

    public function showCreateModal()
    {
        $this->editingId = null;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function showDetailModal($id)
    {
        $this->detailJourFerieId = $id;
        $this->showDetail = true;
    }

    public function closeDetailModal()
    {
        $this->showDetail = false;
        $this->detailJourFerieId = null;
    }

    /**
     * Générer directement les jours fériés (avec confirmation JavaScript)
     */
    public function generateHolidays()
    {
        Log::info('generateHolidays appelée');
        
        $this->isGenerating = true;
        
        try {
            // Vérifier si le package est installé
            if (!class_exists(\Spatie\Holidays\Holidays::class)) {
                Log::error('Package spatie/holidays non trouvé');
                session()->flash('error', 'Le package spatie/holidays n\'est pas installé.');
                $this->isGenerating = false;
                return;
            }

            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                Log::warning('Aucune année budgétaire active trouvée');
                session()->flash('error', 'Aucune année budgétaire active trouvée.');
                $this->isGenerating = false;
                return;
            }

            Log::info('Année budgétaire trouvée', ['annee' => $anneeBudgetaire->libelle]);

            $service = app(HolidayGeneratorService::class);
            Log::info('Service HolidayGeneratorService créé');
            
            // Vérifier si déjà généré
            if ($service->hasHolidaysGenerated($anneeBudgetaire, $this->codeTravail)) {
                Log::warning('Jours fériés déjà générés');
                session()->flash('warning', 'Les jours fériés ont déjà été générés pour cette année.');
                $this->isGenerating = false;
                return;
            }

            // Générer les jours fériés
            Log::info('Début de la génération des jours fériés...');
            $result = $service->generateHolidaysForYear($anneeBudgetaire, $this->codeTravail);

            if ($result['success']) {
                Log::info('Génération réussie', ['count' => $result['count']]);
                session()->flash('success', $result['message']);
                $this->resetPage(); // Rafraîchir la liste
            } else {
                Log::error('Génération échouée', ['message' => $result['message']]);
                session()->flash('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Erreur lors de la génération : ' . $e->getMessage());
        } finally {
            $this->isGenerating = false;
        }
    }

    /**
     * Supprimer les jours fériés générés
     */
    public function deleteGeneratedHolidays()
    {
        try {
            $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
            
            if (!$anneeBudgetaire) {
                session()->flash('error', 'Aucune année budgétaire active trouvée.');
                return;
            }

            $service = app(HolidayGeneratorService::class);
            
            if ($service->deleteGeneratedHolidays($anneeBudgetaire, $this->codeTravail)) {
                session()->flash('success', 'Jours fériés générés supprimés avec succès.');
                $this->resetPage();
            } else {
                session()->flash('error', 'Erreur lors de la suppression.');
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // === MÉTHODES EXISTANTES ===

    public function handleJourFerieCreated()
    {
        $this->closeModal();
        session()->flash('success', 'Jour férié ajouté avec succès.');
    }

    public function handleJourFerieUpdated()
    {
        $this->closeModal();
        session()->flash('success', 'Jour férié modifié avec succès.');
    }

    public function getJoursFeriesProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return collect();
        }

        return Configuration::joursFeries()
            ->forCodeTravail($this->codeTravailId)
            ->forAnnee($anneeBudgetaire->id)
            ->searchByLibelle($this->searchLibelle)
            ->orderBy('date')
            ->paginate(10);
    }

    public function filter()
    {
        $this->isFiltering = true;
        sleep(1);
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function resetFilters()
    {
        $this->isFiltering = true;
        sleep(1);
        $this->searchLibelle = '';
        $this->resetPage();
        $this->isFiltering = false;
    }

    public function getDetailJourFerieProperty()
    {
        if ($this->detailJourFerieId) {
            return Configuration::with(['codeTravail', 'anneeBudgetaire'])->find($this->detailJourFerieId);
        }
        return null;
    }

    public function getAnneeBudgetaireActiveProperty()
    {
        return AnneeFinanciere::where('actif', true)->first();
    }

    public function getTitleModalProperty()
    {
        return strtolower($this->codeTravail->libelle);
    }

    /**
     * Vérifier si les jours fériés ont été générés
     */
    public function getHasGeneratedHolidaysProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return false;
        }

        try {
            $service = app(HolidayGeneratorService::class);
            return $service->hasHolidaysGenerated($anneeBudgetaire, $this->codeTravail);
        } catch (\Exception $e) {
            Log::error('Erreur dans getHasGeneratedHolidaysProperty', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Obtenir le nombre de jours fériés générés
     */
    public function getGeneratedHolidaysCountProperty()
    {
        $anneeBudgetaire = AnneeFinanciere::where('actif', true)->first();
        
        if (!$anneeBudgetaire) {
            return 0;
        }

        try {
            $service = app(HolidayGeneratorService::class);
            return $service->getExistingHolidaysCount($anneeBudgetaire, $this->codeTravail);
        } catch (\Exception $e) {
            Log::error('Erreur dans getGeneratedHolidaysCountProperty', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    public function render()
    {
        return view('rhfeuilledetempsconfig::livewire.jour.jours-feries-list', [
            'joursFeries' => $this->joursFeries,
            'detailJourFerie' => $this->detailJourFerie,
            'anneeBudgetaireActive' => $this->anneeBudgetaireActive,
            'titleModal' => $this->titleModal,
            'hasGeneratedHolidays' => $this->hasGeneratedHolidays,
            'generatedHolidaysCount' => $this->generatedHolidaysCount,
        ]);
    }
}