<?php

namespace Modules\RhFeuilleDeTempsConfig\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class ComportementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('rhfeuilledetempsconfig::index');
    }

    /**
     * Afficher la configuration d'un code de travail
     */
    public function configure($codeTravailId)
    {
        $codeTravail = CodeTravail::with('categorie')->findOrFail($codeTravailId);
        
        // Vérifier que le code est configurable
        if (!$codeTravail->isConfigurable()) {
            return redirect()->back()->with('error', 'Ce code de travail n\'est pas configurable.');
        }
        
        // Rediriger selon le type de configuration
        $valeurConfig = $codeTravail->categorie->valeur_config;
        
        switch ($valeurConfig) {
            case 'Jour':
                return $this->configureJoursFeries($codeTravail);
            case 'Individuel':
                return $this->configureIndividuel($codeTravail);
            case 'Collectif':
                return $this->configureCollectif($codeTravail);
            default:
                return redirect()->back()->with('error', 'Type de configuration non supporté.');
        }
    }
    
    /**
     * Configuration pour les jours fériés
     */
    private function configureJoursFeries($codeTravail)
    {
        return view('rhfeuilledetempsconfig::jours-feries', [
            'codeTravail' => $codeTravail
        ]);
    }
    
    /**
     * Configuration individuelle d'un employé
     */
    private function configureIndividuel($codeTravail)
    {
        // À implémenter plus tard
        return view('rhfeuilledetempsconfig::individuel', [
            'codeTravail' => $codeTravail
        ]);
    }
    
    /**
     * Configuration collective pour des employés
     */
    private function configureCollectif($codeTravail)
    {
        return view('rhfeuilledetempsconfig::collectif', [
            'codeTravail' => $codeTravail
        ]);
    }
}
