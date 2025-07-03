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
                // NOUVELLE RÈGLE: Seul le gestionnaire direct peut valider/rejeter
                // L'admin ne peut plus valider/rejeter sauf s'il est le gestionnaire direct
                $isDirectManager = $operationEmploye->gestionnaire_id === $userEmploye->id;
                
                if (!$isDirectManager) {
                    $event->setBlocked(true, 'Seul le gestionnaire direct de l\'employé peut valider/rejeter cette feuille de temps');
                }
                break;

            case 'retourner':
                // NOUVELLE RÈGLE: Pour le rappel, on vérifie l'état actuel
                $currentState = $operation->getCurrentState();
                
                if ($currentState === 'valide') {
                    // Une feuille validée ne peut être rappelée que par un admin
                    if (!$user->hasRole('ADMIN')) {
                        $event->setBlocked(true, 'Seul un administrateur peut rappeler une feuille de temps validée');
                    }
                } else {
                    // Pour les autres états (rejeté), admin uniquement
                    if (!$user->hasRole('ADMIN')) {
                        $event->setBlocked(true, 'Seul un administrateur peut retourner cette opération');
                    }
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
                // NOUVELLE RÈGLE: Seul le gestionnaire direct peut valider/rejeter
                // L'admin ne peut plus valider/rejeter sauf s'il est le gestionnaire direct
                $isDirectManager = $demandeEmploye->gestionnaire_id === $userEmploye->id;
                
                if (!$isDirectManager) {
                    $event->setBlocked(true, 'Seul le gestionnaire direct de l\'employé peut valider/rejeter cette demande');
                }
                break;

            case 'retourner':
                // NOUVELLE RÈGLE: Pour le rappel, on vérifie l'état actuel
                $currentState = $demande->getCurrentPlace(); // Supposant que DemandeAbsence utilise aussi HasWorkflow
                
                if ($currentState === 'Validé') {
                    // Une demande validée ne peut être rappelée que par un admin
                    if (!$user->hasRole('ADMIN')) {
                        $event->setBlocked(true, 'Seul un administrateur peut rappeler une demande d\'absence validée');
                    }
                } else {
                    // Pour les autres états (rejeté), admin uniquement
                    if (!$user->hasRole('ADMIN')) {
                        $event->setBlocked(true, 'Seul un administrateur peut retourner cette demande');
                    }
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