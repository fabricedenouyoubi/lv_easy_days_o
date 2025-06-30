<?php

namespace Modules\RhFeuilleDeTempsAbsence\Traits;

use Illuminate\Support\Facades\Auth;
use Workflow\Events\GuardEvent;
use Workflow\Registry;

trait HasWorkflow
{
    /**
     * Obtenir le nom du workflow pour ce modèle
     */
    public function getWorkflowName(): string
    {
        return match (get_class($this)) {
            'Modules\RhFeuilleDeTempsAbsence\Models\Operation' => 'operation_workflow',
            'Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence' => 'demande_absence_workflow',
            default => throw new \InvalidArgumentException('Aucun workflow défini pour ce modèle')
        };
    }

    /**
     * Obtenir l'instance du workflow
     */
    public function workflow()
    {
        return app(Registry::class)->get($this, $this->getWorkflowName());
    }

    /**
     * Vérifier si une transition est possible
     */
    public function canTransition(string $transition): bool
    {
        return $this->workflow()->can($this, $transition);
    }

    /**
     * Appliquer une transition
     */
    public function applyTransition(string $transition, array $context = []): void
    {
        $fromState = $this->getCurrentPlace();

        // Événement avant transition
        $this->beforeTransition($transition, $fromState, $context);

        // Appliquer la transition
        $this->workflow()->apply($this, $transition, $context);

        // Événement après transition
        $this->afterTransition($transition, $fromState, $this->getCurrentPlace(), $context);

        // Sauvegarder automatiquement
        $this->save();
    }

    /**
     * Obtenir l'état actuel
     */
    public function getCurrentPlace(): string
    {
        $places = $this->workflow()->getMarking($this)->getPlaces();
        return array_keys($places)[0] ?? $this->getDefaultState();
    }

    /**
     * Obtenir les transitions disponibles
     */
    public function getEnabledTransitions(): array
    {
        return $this->workflow()->getEnabledTransitions($this);
    }

    /**
     * Obtenir tous les états possibles
     */
    public function getPlaces(): array
    {
        return $this->workflow()->getDefinition()->getPlaces();
    }

    /**
     * Vérifier si l'objet est dans un état spécifique
     */
    public function isInPlace(string $place): bool
    {
        return $this->getCurrentPlace() === $place;
    }

    /**
     * État par défaut
     */
    protected function getDefaultState(): string
    {
        return match (get_class($this)) {
            'Modules\RhFeuilleDeTempsAbsence\Models\Operation' => 'brouillon',
            'Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence' => 'Brouillon',
            default => 'brouillon'
        };
    }

    /**
     * Événement avant transition (peut être surchargé)
     */
    protected function beforeTransition(string $transition, string $fromState, array $context = []): void
    {
        // Logique personnalisée avant transition
    }

    /**
     * Événement après transition (peut être surchargé)
     */
    protected function afterTransition(string $transition, string $fromState, string $toState, array $context = []): void
    {
        // Logger la transition
        $this->logTransition($fromState, $toState, $context['comment'] ?? null);

        // Notifications ou autres actions post-transition
        $this->handlePostTransition($transition, $fromState, $toState, $context);
    }

    /**
     * Gérer les actions post-transition (peut être surchargé)
     */
    protected function handlePostTransition(string $transition, string $fromState, string $toState, array $context = []): void
    {
        // Actions spécifiques selon la transition
        match ($transition) {
            'valider' => $this->onValidation($context),
            'rejeter' => $this->onRejection($context),
            'soumettre' => $this->onSubmission($context),
            default => null
        };
    }

    /**
     * Actions lors de la validation (peut être surchargé)
     */
    protected function onValidation(array $context = []): void
    {
        // Actions spécifiques à la validation
    }

    /**
     * Actions lors du rejet (peut être surchargé)
     */
    protected function onRejection(array $context = []): void
    {
        // Actions spécifiques au rejet
    }

    /**
     * Actions lors de la soumission (peut être surchargé)
     */
    protected function onSubmission(array $context = []): void
    {
        // Actions spécifiques à la soumission
    }

    /**
     * Logger une transition (méthode existante améliorée)
     */
    public function logTransition(string $from, string $to, ?string $comment = null): void
    {
        $log = [
            'timestamp' => now()->format('Y-m-d H:i'),
            'date' => now()->format('d-m-Y'),
            'time' => now()->format('H:i'),
            'from_state' => $from,
            'to_state' => $to,
            'comment' => $comment ?? '',
            'title' => "{$from} → {$to}",
            'user' => Auth::user()->name ?? 'System',
            'user_id' => Auth::user()->id ?? null
        ];

        $logs = $this->workflow_log ? explode("\n", $this->workflow_log) : [];
        $logs[] = json_encode($log);

        // Mise à jour du log (sans déclencher save() pour éviter boucle)
        $this->updateQuietly(['workflow_log' => implode("\n", $logs)]);
    }



    /**
     * Obtenir l'historique des transitions
     */
    public function getWorkflowHistory(): array
    {
        if (!$this->workflow_log) {
            return [];
        }

        return collect(explode("\n", $this->workflow_log))
            ->filter()
            ->map(fn($line) => json_decode(trim($line), true))
            ->filter()
            ->reverse()
            ->values()
            ->toArray();
    }
}
