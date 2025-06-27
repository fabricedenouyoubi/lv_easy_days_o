<?php

namespace App\Guards;

use Modules\RhFeuilleDeTempsAbsence\Models\Operation;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Workflow\Event\GuardEvent;

class ManagerGuard
{
    /**
     * Vérifier les permissions pour les transitions workflow
     */
    public function __invoke(GuardEvent $event)
    {
        $subject = $event->getSubject();
        $user = auth()->user();
        $transition = $event->getTransition()->getName();

        // Vérifications de base
        if (!$user || !$user->employe) {
            $event->setBlocked(true, 'Utilisateur non authentifié ou sans employé associé');
            return;
        }

        // Gestion des permissions selon le type d'objet
        if ($subject instanceof Operation) {
            $this->checkOperationPermissions($event, $subject, $user, $transition);
        } elseif ($subject instanceof DemandeAbsence) {
            $this->checkDemandeAbsencePermissions($event, $subject, $user, $transition);
        }
    }

    /**
     * Vérifier les permissions pour les Opérations
     */
    protected function checkOperationPermissions(GuardEvent $event, Operation $operation, $user, string $transition)
    {
        $userEmploye = $user->employe;
        $operationEmploye = $operation->employe;

        switch ($transition) {
            case 'commencer':
            case 'soumettre':
            case 'rappeler':
                // L'employé propriétaire peut effectuer ces actions
                if ($userEmploye->id !== $operationEmploye->id && !$user->hasRole('ADMIN')) {
                    $event->setBlocked(true, 'Seul le propriétaire peut effectuer cette action');
                }
                break;

            case 'valider':
            case 'rejeter':
                // Seul le gestionnaire de l'employé ou un admin peut valider/rejeter
                $isManager = $operationEmploye->gestionnaire_id === $userEmploye->id;
                $isAdmin = $user->hasRole('ADMIN');
                
                if (!$isManager && !$isAdmin) {
                    $event->setBlocked(true, 'Seul le gestionnaire ou un administrateur peut valider/rejeter');
                }
                break;

            case 'retourner':
                // Admin uniquement pour retourner une opération validée/rejetée
                if (!$user->hasRole('ADMIN')) {
                    $event->setBlocked(true, 'Seul un administrateur peut retourner cette opération');
                }
                break;
        }
    }

    /**
     * Vérifier les permissions pour les Demandes d'Absence
     */
    protected function checkDemandeAbsencePermissions(GuardEvent $event, DemandeAbsence $demande, $user, string $transition)
    {
        $userEmploye = $user->employe;
        $demandeEmploye = $demande->employe;

        switch ($transition) {
            case 'commencer':
            case 'soumettre':
            case 'rappeler':
                // L'employé propriétaire ou celui qui a créé la demande (admin_id)
                $isOwner = $userEmploye->id === $demandeEmploye->id;
                $isCreator = $demande->admin_id === $user->id;
                $isAdmin = $user->hasRole('ADMIN');
                
                if (!$isOwner && !$isCreator && !$isAdmin) {
                    $event->setBlocked(true, 'Seul le propriétaire ou le créateur peut effectuer cette action');
                }
                break;

            case 'valider':
            case 'rejeter':
                // Seul le gestionnaire de l'employé ou un admin peut valider/rejeter
                $isManager = $demandeEmploye->gestionnaire_id === $userEmploye->id;
                $isAdmin = $user->hasRole('ADMIN');
                
                if (!$isManager && !$isAdmin) {
                    $event->setBlocked(true, 'Seul le gestionnaire ou un administrateur peut valider/rejeter');
                }
                break;

            case 'retourner':
                // Admin uniquement pour retourner une demande validée/rejetée
                if (!$user->hasRole('ADMIN')) {
                    $event->setBlocked(true, 'Seul un administrateur peut retourner cette demande');
                }
                break;
        }
    }

    /**
     * Vérifications supplémentaires selon l'état de l'objet
     */
    protected function checkStateConstraints(GuardEvent $event, $subject, string $transition)
    {
        // empêcher la modification d'une opération après une certaine date
        if ($subject instanceof Operation && $transition === 'soumettre') {
            $semaine = $subject->anneeSemaine;
            if ($semaine && $semaine->fin < now()->subDays(7)) {
                $event->setBlocked(true, 'Impossible de soumettre une feuille de temps de plus de 7 jours');
            }
        }
    }
}