<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*  ContentType

            ['app_label' => 'rh', 'model' => 'employe'], // id => 1
            ['app_label' => 'rh', 'model' => 'historique_gestionnaire'], // id => 2

        */
        $permissions = [
            ['name' => 'Peut Acceder au menu employe', 'content_type_id' => 1, 'codename' => 'can_manage_employes'], // access pour le menu employe
            ['name' => 'Peut Ajouter les employes', 'content_type_id' => 1, 'codename' => 'can_add_employes'], // access pour ajouter des employes
            ['name' => 'Peut Modifier les employes', 'content_type_id' => 1, 'codename' => 'can_edit_employes'], // access pour modifier des employe
            ['name' => 'Peut Voir de le details des employes', 'content_type_id' => 1, 'codename' => 'can_view_details_employes'], // access pour voir le details des employes
            ['name' => 'Peut modifier le gestionnaire d\'un employe', 'content_type_id' => 2, 'codename' => 'can_edit_gestionnaire_employe'], // modifier le gestionnaire d'un employe
        ];

        DB::table('permissions')->insert($permissions);
    }
}
