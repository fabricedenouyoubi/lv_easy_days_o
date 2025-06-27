<?php

namespace App\Guards;

use Workflow\Event\GuardEvent;

class RejetGuard
{
    /**
     * Vérifier que le motif de rejet est fourni
     */
    public function __invoke(GuardEvent $event)
    {
        $context = $event->getContext();
        $user = auth()->user();

        // Vérifications de base
        if (!$user || !$user->employe) {
            $event->setBlocked(true, 'Utilisateur non authentifié');
            return;
        }

        // Vérifier que le motif de rejet est fourni
        if (empty($context['motif_rejet']) || trim($context['motif_rejet']) === '') {
            $event->setBlocked(true, 'Le motif de rejet est obligatoire');
            return;
        }

        // Vérifier que le motif fait au moins 5 caractères
        if (strlen(trim($context['motif_rejet'])) < 5) {
            $event->setBlocked(true, 'Le motif de rejet doit contenir au moins 5 caractères');
            return;
        }

        // Vérifier les permissions manager (réutiliser la logique du ManagerGuard)
        $subject = $event->getSubject();
        $userEmploye = $user->employe;

        // Selon le type d'objet, vérifier les permissions
        if (method_exists($subject, 'employe')) {
            $subjectEmploye = $subject->employe;
            $isManager = $subjectEmploye->gestionnaire_id === $userEmploye->id;
            $isAdmin = $user->hasRole('ADMIN');
            
            if (!$isManager && !$isAdmin) {
                $event->setBlocked(true, 'Seul le gestionnaire ou un administrateur peut rejeter');
            }
        }
    }
}