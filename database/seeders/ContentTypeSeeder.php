<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['app_label' => 'auth', 'model' => 'permission'],
            ['app_label' => 'auth', 'model' => 'group'],
            ['app_label' => 'contenttypes', 'model' => 'contenttype'],
            ['app_label' => 'sessions', 'model' => 'session'],
            ['app_label' => 'sites', 'model' => 'site'],
            ['app_label' => 'admin', 'model' => 'logentry'],
            ['app_label' => 'account', 'model' => 'emailaddress'],
            ['app_label' => 'account', 'model' => 'emailconfirmation'],
            ['app_label' => 'socialaccount', 'model' => 'socialaccount'],
            ['app_label' => 'socialaccount', 'model' => 'socialapp'],
            ['app_label' => 'socialaccount', 'model' => 'socialtoken'],
            ['app_label' => 'users', 'model' => 'user'],
            ['app_label' => 'entreprise', 'model' => 'entreprise'],
            ['app_label' => 'entreprise', 'model' => 'site'],
            ['app_label' => 'entreprise', 'model' => 'addresse'],
            ['app_label' => 'budget', 'model' => 'anneefinanciere'],
            ['app_label' => 'rh', 'model' => 'employe'],
            ['app_label' => 'rh', 'model' => 'poste'],
            ['app_label' => 'rh', 'model' => 'historiqueposte'],
            ['app_label' => 'rh', 'model' => 'historiquegestionnaire'],
            ['app_label' => 'rh', 'model' => 'historiqueadresse'],
            ['app_label' => 'rh_feuille_de_temps_config', 'model' => 'codedetravail'],
            ['app_label' => 'rh_feuille_de_temps_config', 'model' => 'configurationcodedetravail'],
            ['app_label' => 'rh_feuille_de_temps_config', 'model' => 'feuilledetemps'],
            ['app_label' => 'rh_feuille_de_temps_config', 'model' => 'employeaffecteaucodedetravail'],
            ['app_label' => 'rh_feuille_de_temps', 'model' => 'demandeabsence'],
            ['app_label' => 'rh_feuille_de_temps', 'model' => 'operation'],
            ['app_label' => 'rh_feuille_de_temps', 'model' => 'lignedetravail'],
            ['app_label' => 'rh_feuille_de_temps', 'model' => 'heurereguliere'],
            ['app_label' => 'background_task', 'model' => 'completedtask'],
            ['app_label' => 'background_task', 'model' => 'task'],
        ];

        foreach ($data as $entry) {
            ContentType::updateOrCreate($entry, $entry);
        }
    }
}
