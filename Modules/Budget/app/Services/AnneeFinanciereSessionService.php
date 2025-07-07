<?php

namespace Modules\Budget\Services;

use Modules\Budget\Models\AnneeFinanciere;
use Illuminate\Support\Facades\Session;

class AnneeFinanciereSessionService
{
    const SESSION_KEY = 'annee_financiere_courante';
    const SESSION_ID_KEY = 'annee_financiere_id';
    const SESSION_DEBUT_KEY = 'annee_debut';
    const SESSION_FIN_KEY = 'annee_fin';

    /**
     * Récupérer l'année financière courante depuis la session ou la base de données
     */
    public static function getAnneeCourante()
    {
        // Vérifier si l'année est déjà en session
        if (Session::has(self::SESSION_KEY)) {
            return Session::get(self::SESSION_KEY);
        }

        // Sinon, récupérer depuis la base de données
        $anneeActive = AnneeFinanciere::getAnneeActive();
        
        if ($anneeActive) {
            self::setAnneeCourante($anneeActive);
            return $anneeActive;
        }

        return null;
    }

    /**
     * Définir l'année financière courante en session
     */
    public static function setAnneeCourante(AnneeFinanciere $anneeFinanciere)
    {
        Session::put([
            self::SESSION_KEY => $anneeFinanciere,
            self::SESSION_ID_KEY => $anneeFinanciere->id,
            self::SESSION_DEBUT_KEY => $anneeFinanciere->debut->format('d-m-Y'),
            self::SESSION_FIN_KEY => $anneeFinanciere->fin->format('d-m-Y')
        ]);
    }

    /**
     * Obtenir l'ID de l'année financière courante
     */
    public static function getAnneeId()
    {
        return Session::get(self::SESSION_ID_KEY);
    }

    /**
     * Obtenir la période formatée de l'année courante
     */
    public static function getPeriodeFormatee()
    {
        $annee = self::getAnneeCourante();
        return $annee ? $annee->libelle : null;
    }

    /**
     * Vérifier si une année financière est définie
     */
    public static function hasAnneeCourante()
    {
        return Session::has(self::SESSION_KEY) && Session::get(self::SESSION_KEY) !== null;
    }

    /**
     * Vider la session de l'année financière
     */
    public static function clearSession()
    {
        Session::forget([
            self::SESSION_KEY,
            self::SESSION_ID_KEY,
            self::SESSION_DEBUT_KEY,
            self::SESSION_FIN_KEY
        ]);
    }

    /**
     * Actualiser l'année financière courante
     */
    public static function refresh()
    {
        self::clearSession();
        return self::getAnneeCourante();
    }
}