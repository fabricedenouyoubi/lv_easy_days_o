<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void

    {
        //--- Permission du module RH ---
        Permission::firstOrCreate(['name' => 'Voir Module RH', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Detail Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Changer Gestionnaire Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Changer Heure Semaine Employé', 'module' => 'RH', 'guard_name' => 'web']);

        //--- Permission module Presentation
        Permission::firstOrCreate(['name' => 'Voir Module PRESENTATION', 'module' => 'PRESENTATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier les informations de l\'entreprise', 'module' => 'PRESENTATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter un nouveau site', 'module' => 'PRESENTATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier un site', 'module' => 'PRESENTATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir les détails d\'un site', 'module' => 'PRESENTATION', 'guard_name' => 'web']);

        //--- Permission module Annee financiere
        Permission::firstOrCreate(['name' => 'Voir Module ANNEE_FINANCIERE', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter une année financière', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Activer une année financière', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Clôturer une année financière', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Générer les semaines d\'une année', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Activer une semaine', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Marquer une semaine de paie', 'module' => 'ANNEE_FINANCIERE', 'guard_name' => 'web']);

        //--- Permission module Configuration
        Permission::firstOrCreate(['name' => 'Voir Module CONFIGURATION', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter un code de travail', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier un code de travail', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Configurer un code de travail', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter une catégorie', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier une catégorie', 'module' => 'CONFIGURATION', 'guard_name' => 'web']);


        //--- Permission module AUTORISATION
        //--- Groupes
        Permission::firstOrCreate(['name' => 'Voir Module AUTORISATION', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Groupes', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Groupe', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Groupe', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Permissions Groupe', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Permissions Groupe', 'module' => 'AUTORISATION', 'guard_name' => 'web']);

        //--- Utilisateurs
        Permission::firstOrCreate(['name' => 'Voir Utilisateurs', 'module' => 'AUTORISATION', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Permissions Utilisateur', 'module' => 'AUTORISATION', 'guard_name' => 'web']);

        //--- Permission module JOURNALISATION
        Permission::firstOrCreate(['name' => 'Voir Journalisation', 'module' => 'JOURNALISATION', 'guard_name' => 'web']);
    }
}
