<?php

namespace Modules\Budget\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Budget\Services\AnneeFinanciereService;

class SemaineController extends Controller
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
        return view('budget::index');
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

            return view('budget::details-annee', [
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
            $feuilleGenerator = app('Modules\Budget\Services\SemaineGeneratorService');
            
            if ($feuilleGenerator->areFeuillesGenerated($anneeFinanciere)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les semaines ont déjà été générées pour cette année.'
                ]);
            }

            $feuilleGenerator->generateFeuillesDeTemps($anneeFinanciere);

            return response()->json([
                'success' => true,
                'message' => 'semaines de l\'année générées avec succès.',
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
     * Régénérer toutes les données d'une année
     */
    public function regenererAnnee(Request $request, $anneeId)
    {
        try {
            $anneeFinanciere = AnneeFinanciere::findOrFail($anneeId);
            
            // Régénérer les feuilles de temps
            $feuilleGenerator = app('Modules\Budget\Services\SemaineGeneratorService');
            $feuilleGenerator->regenerateFeuillesDeTemps($anneeFinanciere);

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
