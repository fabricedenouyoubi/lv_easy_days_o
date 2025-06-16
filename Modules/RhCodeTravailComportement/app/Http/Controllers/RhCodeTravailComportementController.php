<?php

namespace Modules\RhCodeTravailComportement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class RhCodeTravailComportementController extends Controller
{
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
        return view('rhcodetravailcomportement::jours-feries', [
            'codeTravail' => $codeTravail
        ]);
    }
    
    /**
     * Configuration individuelle (pour plus tard)
     */
    private function configureIndividuel($codeTravail)
    {
        // À implémenter plus tard
        return view('rhcodetravailcomportement::individuel', [
            'codeTravail' => $codeTravail
        ]);
    }
    
    /**
     * Configuration collective (pour plus tard)
     */
    private function configureCollectif($codeTravail)
    {
        // À implémenter plus tard
        return view('rhcodetravailcomportement::collectif', [
            'codeTravail' => $codeTravail
        ]);
    }
}