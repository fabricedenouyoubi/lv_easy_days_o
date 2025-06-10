<?php

namespace Modules\Entreprise\Livewire;

use Livewire\Component;
use Modules\Entreprise\Models\Adresse;
use Modules\Entreprise\Models\Entreprise;
use Modules\Entreprise\Models\Site;

class SiteForm extends Component
{
// Site
    public $siteId;
    public $name;
    public $description;
    
    // Adresse
    public $adresseId;
    public $rue;
    public $ville;
    public $code_postal;
    public $appartement;
    public $telephone;
    public $telephone_pro;
    public $telephone_pro_ext;

    protected $listeners = ['editSite'];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'rue' => 'required|string|max:50',
            'ville' => 'required|string|max:60',
            'code_postal' => 'required|string|max:7',
            'appartement' => 'nullable|string|max:10',
            'telephone' => 'required|string|max:12|regex:/^[0-9+]+$/',
            'telephone_pro' => 'nullable|string|max:12|regex:/^[0-9+]+$/',
            'telephone_pro_ext' => 'nullable|string|max:5|regex:/^[0-9+]+$/',
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom du site est obligatoire.',
        'rue.required' => 'La rue est obligatoire.',
        'ville.required' => 'La ville est obligatoire.',
        'code_postal.required' => 'Le code postal est obligatoire.',
        'telephone.required' => 'Le téléphone est obligatoire.',
        'telephone.regex' => 'Le téléphone doit contenir uniquement des chiffres.',
        'telephone_pro.regex' => 'Le téléphone pro doit contenir uniquement des chiffres.',
        'telephone_pro_ext.regex' => 'L\'extension doit contenir uniquement des chiffres.',
    ];

    public function mount($siteId = null)
    {
        $this->siteId = $siteId;
        
        if ($siteId) {
            $this->loadSite();
        }
    }

    public function editSite($siteId)
    {
        $this->siteId = $siteId;
        $this->loadSite();
    }

    private function loadSite()
    {
        if ($this->siteId) {
            $site = Site::with('adresse')->findOrFail($this->siteId);
            
            // Charger les données du site
            $this->name = $site->name;
            $this->description = $site->description;
            
            // Charger les données de l'adresse
            if ($site->adresse) {
                $this->adresseId = $site->adresse->id;
                $this->rue = $site->adresse->rue;
                $this->ville = $site->adresse->ville;
                $this->code_postal = $site->adresse->code_postal;
                $this->appartement = $site->adresse->appartement;
                $this->telephone = $site->adresse->telephone;
                $this->telephone_pro = $site->adresse->telephone_pro;
                $this->telephone_pro_ext = $site->adresse->telephone_pro_ext;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Récupérer l'entreprise (première ou depuis session)
            $entreprise = Entreprise::first();
            
            if (!$entreprise) {
                session()->flash('error', 'Aucune entreprise n\'est configurée.');
                return;
            }

            // Créer ou mettre à jour l'adresse
            if ($this->adresseId) {
                $adresse = Adresse::findOrFail($this->adresseId);
                $adresse->update([
                    'rue' => $this->rue,
                    'ville' => $this->ville,
                    'code_postal' => $this->code_postal,
                    'appartement' => $this->appartement,
                    'telephone' => $this->telephone,
                    'telephone_pro' => $this->telephone_pro,
                    'telephone_pro_ext' => $this->telephone_pro_ext,
                ]);
            } else {
                $adresse = Adresse::create([
                    'rue' => $this->rue,
                    'ville' => $this->ville,
                    'code_postal' => $this->code_postal,
                    'appartement' => $this->appartement,
                    'telephone' => $this->telephone,
                    'telephone_pro' => $this->telephone_pro,
                    'telephone_pro_ext' => $this->telephone_pro_ext,
                ]);
            }

            // Créer ou mettre à jour le site
            if ($this->siteId) {
                // Modification
                $site = Site::findOrFail($this->siteId);
                $site->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'adresse_id' => $adresse->id,
                    'entreprise_id' => $entreprise->id,
                ]);
                
                $this->dispatch('siteUpdated');
            } else {
                // Création
                Site::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'adresse_id' => $adresse->id,
                    'entreprise_id' => $entreprise->id,
                ]);
                
                $this->dispatch('siteCreated');
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

    public function render()
    {
        return view('entreprise::livewire.site-form');
    }
}
