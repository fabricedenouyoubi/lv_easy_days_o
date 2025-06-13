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
            ['app_label' => 'rh', 'model' => 'employe'], // id => 1
            ['app_label' => 'rh', 'model' => 'historique_gestionnaire'], // id => 2

        ];

        foreach ($data as $entry) {
            ContentType::updateOrCreate($entry, $entry);
        }
    }
}
