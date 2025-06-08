<?php

namespace Modules\RhFeuilleDeTempsConfig\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\RhFeuilleDeTempsConfig\Services\AnneeFinanciereService;

class RhFeuilleDeTempsConfigController extends Controller
{
    protected $anneeFinanciereService;

    public function __construct(AnneeFinanciereService $anneeFinanciereService)
    {
        $this->anneeFinanciereService = $anneeFinanciereService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('rhfeuilledetempsconfig::index');
    }

    /**
     * Afficher les détails d'une année financière avec ses feuilles de temps
     */
    public function detailsAnnee($anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Mettre à jour la session si c'est l'année active
            if ($anneeFinanciere->actif) {
                $this->anneeFinanciereService->updateAnneeFinanciereSessionData($anneeFinanciere);
            }

            return view('rhfeuilledetempsconfig::details-annee', [
                'anneeFinanciere' => $anneeFinanciere
            ]);
        } catch (\Exception $e) {
            return redirect()->route('budget.annees-financieres')
                           ->with('error', 'Année financière introuvable.');
        }
    }

    /**
     * Générer les feuilles de temps pour une année
     */
    public function genererFeuillesDeTemps(Request $request, $anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Vérifier si les feuilles existent déjà
            $feuilleGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService');
            
            if ($feuilleGenerator->areFeuillesGenerated($anneeFinanciere)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les feuilles de temps ont déjà été générées pour cette année.'
                ]);
            }

            $feuilleGenerator->generateFeuillesDeTemps($anneeFinanciere);

            return response()->json([
                'success' => true,
                'message' => 'Feuilles de temps générées avec succès.',
                'stats' => $feuilleGenerator->getGenerationStats($anneeFinanciere)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer les jours fériés pour une année
     */
    public function genererJoursFeries(Request $request, $anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            $ferieGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\JourFerieGeneratorService');
            $ferieGenerator->generateJourFerie($anneeFinanciere);

            return response()->json([
                'success' => true,
                'message' => 'Jours fériés générés avec succès.',
                'stats' => $ferieGenerator->getGenerationStats($anneeFinanciere)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Régénérer toutes les données d'une année
     */
    public function regenererAnnee(Request $request, $anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Régénérer les feuilles de temps
            $feuilleGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\FeuilleDeTempsGeneratorService');
            $feuilleGenerator->regenerateFeuillesDeTemps($anneeFinanciere);
            
            // Régénérer les jours fériés
            $ferieGenerator = app('Modules\RhFeuilleDeTempsConfig\Services\JourFerieGeneratorService');
            $ferieGenerator->regenerateJourFerie($anneeFinanciere);

            return response()->json([
                'success' => true,
                'message' => 'Année financière régénérée avec succès.',
                'stats' => $this->anneeFinanciereService->getAnneeFinanciereStats($anneeFinanciere)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régénération : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques d'une année
     */
    public function getStatistiques($anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            $stats = $this->anneeFinanciereService->getAnneeFinanciereStats($anneeFinanciere);

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques : ' . $e->getMessage()
            ], 500);
        }
    }
}
