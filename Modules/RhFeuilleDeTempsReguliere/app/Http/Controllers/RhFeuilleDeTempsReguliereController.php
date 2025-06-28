<?php

namespace Modules\RhFeuilleDeTempsReguliere\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsAbsence\Models\Operation;

class RhFeuilleDeTempsReguliereController extends Controller
{
    /**
     * Afficher la liste des feuilles de temps pour l'employé connecté
     */
    public function index(): Renderable
    {
        return view('rhfeuilledetempsreguliere::index');
    }

    /**
     * Éditer une feuille de temps
     */
    public function edit(Request $request, $semaineId, $operationId = null): Renderable
    {
        // Récupérer la semaine
        $semaine = SemaineAnnee::findOrFail($semaineId);
        
        // Récupérer ou créer l'opération
        if ($operationId) {
            $operation = Operation::findOrFail($operationId);
            
            // Vérifier que l'opération appartient bien à l'employé connecté
            if ($operation->employe_id !== auth()->user()->employe->id) {
                abort(403, 'Accès non autorisé à cette feuille de temps');
            }
        } else {
            // Créer une nouvelle opération si elle n'existe pas
            $operation = Operation::getOrCreateOperation(
                auth()->user()->employe->id, 
                $semaineId
            );
        }

        return view('rhfeuilledetempsreguliere::edit', [
            'semaineId' => $semaineId,
            'operationId' => $operation->id,
            'semaine' => $semaine,
            'operation' => $operation
        ]);
    }

    /**
     * Consulter une feuille de temps
     */
    public function show(Request $request, $semaineId, $operationId): Renderable
    {
        $semaine = SemaineAnnee::findOrFail($semaineId);
        $operation = Operation::with(['lignesTravail.codeTravail', 'employe'])->findOrFail($operationId);
        
        // Vérifier les permissions
        $user = auth()->user();
        $canView = $operation->employe_id === $user->employe->id || // Propriétaire
                  $operation->employe->gestionnaire_id === $user->employe->id || // Gestionnaire
                  $user->hasRole('ADMIN'); // Admin
                  
        if (!$canView) {
            abort(403, 'Accès non autorisé à cette feuille de temps');
        }

        return view('rhfeuilledetempsreguliere::show', [
            'semaine' => $semaine,
            'operation' => $operation
        ]);
    }

    /**
     * Tableau de bord gestionnaire
     */
    public function managerDashboard(): Renderable
    {
        return view('rhfeuilledetempsreguliere::dashboard');
    }
}