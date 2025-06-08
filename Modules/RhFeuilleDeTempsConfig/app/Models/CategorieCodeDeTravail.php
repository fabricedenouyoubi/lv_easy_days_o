<?php

namespace Modules\RhFeuilleDeTempsConfig\Models;

//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\RhFeuilleDeTempsConfig\Database\Factories\CategorieCodeDeTravailFactory;

class CategorieCodeDeTravail
{
    // Constantes pour les catégories
    const HEURE_REGULIERE = 'HEURE_REGULIERE';
    const HEURE_SUPPLEMENTAIRE = 'HEURE_SUPPLEMENTAIRE';
    const CONGE = 'CONGE';
    const FORMATION = 'FORMATION';
    const DEPLACEMENT = 'DEPLACEMENT';
    const CAISSE_TEMPS = 'CAISSE_TEMPS';
    const CONGE_MOBILE = 'CONGE_MOBILE';
    const CSN = 'CSN';

    /**
     * Obtenir tous les choix disponibles
     */
    public static function choices()
    {
        return [
            self::HEURE_REGULIERE => 'Heures régulières',
            self::HEURE_SUPPLEMENTAIRE => 'Heures supplémentaires',
            self::CONGE => 'Congé',
            self::FORMATION => 'Formation',
            self::DEPLACEMENT => 'Déplacement',
            self::CAISSE_TEMPS => 'Caisse de temps',
            self::CONGE_MOBILE => 'Congé mobile',
            self::CSN => 'CSN',
        ];
    }

    /**
     * Vérifier si une catégorie est liée aux absences
     */
    public static function isAbsence($categorie)
    {
        return in_array($categorie, [
            self::CONGE,
            self::FORMATION,
            self::CONGE_MOBILE,
            self::CAISSE_TEMPS
        ]);
    }

    /**
     * Obtenir les catégories d'absence
     */
    public static function getAbsenceCategories()
    {
        return [
            self::CONGE,
            self::FORMATION,
            self::CONGE_MOBILE,
            self::CAISSE_TEMPS
        ];
    }

    /**
     * Obtenir les catégories de travail
     */
    public static function getWorkCategories()
    {
        return [
            self::HEURE_REGULIERE,
            self::HEURE_SUPPLEMENTAIRE,
            self::DEPLACEMENT,
            self::CSN
        ];
    }

    /**
     * Obtenir le libellé d'une catégorie
     */
    public static function getLabel($categorie)
    {
        return self::choices()[$categorie] ?? $categorie;
    }

    /**
     * Obtenir toutes les valeurs des constantes
     */
    public static function values()
    {
        return array_keys(self::choices());
    }
}
