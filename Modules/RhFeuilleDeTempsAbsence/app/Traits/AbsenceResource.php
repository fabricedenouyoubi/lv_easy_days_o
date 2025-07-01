<?php

namespace Modules\RhFeuilleDeTempsAbsence\Traits;

use Illuminate\Support\Carbon;
use Modules\Budget\Models\SemaineAnnee;
use Modules\RhFeuilleDeTempsConfig\Models\Comportement\Configuration;

trait AbsenceResource
{
    //--- recuperer les jours non ouvrables pour les exclure dans le calcul des heures d'absence
    public function recupererDateNonOuvrable($idAnneFinnEncours)
    {
        //---Recuperer les Jours feriés
        $jour_feriee_list = Configuration::whereHas('codeTravail.categorie', function ($query) {
            $query->where('intitule', 'Fermé');
        })->pluck('date')->map(fn($date) => Carbon::parse($date)->toDateString())->toArray();

        //--- extraire les dimanches des demanines de l'année ---
        $sundayDates = SemaineAnnee::where('annee_financiere_id', $idAnneFinnEncours)
            ->get()
            ->map(function ($semaine) {
                // Calcule le dimanche à partir de la date de début
                $sunday = Carbon::parse($semaine->debut)->next(Carbon::SUNDAY);
                // Vérifie que ce dimanche est bien dans la semaine
                if ($sunday->between($semaine->debut, $semaine->fin)) {
                    return $sunday->toDateString(); // ou format('d/m/Y')
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();

        $date = collect($jour_feriee_list)
            ->merge($sundayDates)
            ->unique() // elimine les doublons
            ->sort()   // classe par ordre croissant
            ->values() // Réindexe les clés de 0 à n-1
            ->all(); // Retourne un tableau
        return $date;
    }

    //--- Calcul du nombre de jour d'absence en tenant compte des jour non ouvrables
    function nombreDeJoursEntre($date_debut, $date_fin, $idAnneFinnEncours): int
    {
        // Conversion des dates en objets Carbon (début de journée)
        $dateDebut = $date_debut instanceof Carbon
            ? $date_debut->copy()->startOfDay()
            : Carbon::parse($date_debut)->startOfDay();

        $dateFin = $date_fin instanceof Carbon
            ? $date_fin->copy()->startOfDay()
            : Carbon::parse($date_fin)->startOfDay();

        //--- recuperation des jours non ouvrables
        $jour_non_ouvrable_list = $this->recupererDateNonOuvrable($idAnneFinnEncours);

        // Normalisation des jours non ouvrables au format 'Y-m-d' avec array_flip pour accès rapide
        $joursNonOuvrables = array_flip(
            array_map(fn($date) => Carbon::parse($date)->toDateString(), $jour_non_ouvrable_list)
        );

        // Génère la liste des jours entre les deux dates incluses
        $joursOuvrables = collect(Carbon::parse($dateDebut)->daysUntil($dateFin->copy()))
            // Supprime les jours non ouvrables
            ->reject(fn(Carbon $date) => isset($joursNonOuvrables[$date->toDateString()]))
            ->count();

        return $joursOuvrables;
    }

    //--- calcul la l'heure total de l'absence
    public function calculateTotalHeures($date_debut, $date_fin, $heure_par_jour, $idAnneFinnEncours): float|int
    {
        // Vérifie que toutes les données nécessaires sont bien présentes
        if (!$date_debut || !$date_fin || !$heure_par_jour) {
            return 0;
        }

        // Conversion des dates en objets Carbon, en commençant au début de la journée
        $dateDebut = $date_debut instanceof Carbon
            ? $date_debut->copy()->startOfDay()
            : Carbon::parse($date_debut)->startOfDay();

        $dateFin = $date_fin instanceof Carbon
            ? $date_fin->copy()->startOfDay()
            : Carbon::parse($date_fin)->startOfDay();

        //--- recuperation des jours non ouvrables
        $jour_non_ouvrable_list = $this->recupererDateNonOuvrable($idAnneFinnEncours);

        // On prépare les jours non ouvrables sous forme de tableau associatif pour un accès rapide avec isset()
        // Chaque date est convertie au format 'Y-m-d'
        $joursNonOuvrables = array_flip(
            array_map(fn($date) => Carbon::parse($date)->toDateString(), $jour_non_ouvrable_list)
        );

        // Création d'une collection de tous les jours entre dateDebut et dateFin inclus
        $totalJoursOuvrables = collect(Carbon::parse($dateDebut)->daysUntil($dateFin->copy()))
            // Exclut les jours qui sont dans la liste des jours non ouvrables
            ->reject(fn(Carbon $date) => isset($joursNonOuvrables[$date->toDateString()]))
            ->count(); // Compte les jours restants (ouvrables)

        // On multiplie le nombre de jours ouvrables par le nombre d'heures par jour pour obtenir le total
        return $totalJoursOuvrables * $heure_par_jour;
    }

    /**
     * Obtenir le statut formaté pour l'affichage
     */
    public function getStatutFormate($statut)
    {
        if (in_array($statut, ['Brouillon', 'brouillon'])) {
            return [
                'text' => 'Brouillon',
                'class' => 'bg-warning text-dark',
                'icon' => 'fas fa-pencil-alt'
            ];
        } elseif (in_array($statut, ['En cours', 'en_cours'])) {
            return [
                'text' => 'En cours',
                'class' => 'bg-info text-dark',
                'icon' => 'fas fa-hourglass-half'
            ];
        } elseif (in_array($statut, ['Soumis', 'soumis'])) {
            return [
                'text' => 'Soumis',
                'class' => 'bg-primary',
                'icon' => 'fas fa-inbox'
            ];
        } elseif (in_array($statut, ['Validé', 'valide'])) {
            return [
                'text' => 'Validé',
                'class' => 'bg-success',
                'icon' => 'fas fa-check-circle'
            ];
        } elseif (in_array($statut, ['Rejeté', 'rejete'])) {
            return [
                'text' => 'Rejeté',
                'class' => 'bg-danger',
                'icon' => 'fas fa-times-circle'
            ];
        } else {
            return [
                'text' => 'Inconnu',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-question-circle'
            ];
        }
    }
}
