<?php

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Rh\Models\Employe\Employe;
use Modules\Rh\Models\Employe\HistoriqueGestionnaire;
use Modules\Rh\Models\Employe\HistoriqueHeuresSemaines;

class EmployeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'nom' => 'Admin Plateforme',
                'prenom' => 'GCS',
                'date_de_naissance' => Carbon::parse('1990-01-01'),
                'date_embauche' => Carbon::parse('2020-01-15 06:00:00+01'),
                'adresse_id' => null,
                'user_id' => 1,
                'matricule' => 'EMP2025-001',
                'est_gestionnaire' => true,
            ],
            [
                'nom' => 'Gravel',
                'prenom' => 'Estelle',
                'date_de_naissance' => Carbon::parse('2000-05-20'),
                'date_embauche' => Carbon::parse('2020-01-15 06:00:00+01'),
                'adresse_id' => null,
                'user_id' => 2,
                'matricule' => 'EMP2025-0011',
            ],
            [
                'nom' => 'Duval',
                'prenom' => 'Eric',
                'date_de_naissance' => Carbon::parse('1985-03-15'),
                'date_embauche' => Carbon::parse('2018-05-20 06:00:00+02'),
                'adresse_id' => null,
                'user_id' => 3,
                'matricule' => 'EMP2025-002',
                'est_gestionnaire' => true,
            ],
            [
                'nom' => 'Gagnon',
                'prenom' => 'Phillipe',
                'date_de_naissance' => Carbon::parse('1988-07-20'),
                'date_embauche' => Carbon::parse('2021-03-10 06:00:00+01'),
                'adresse_id' => null,
                'user_id' => 4,
                'matricule' => 'EMP2025-003',
            ],
            [
                'nom' => 'Beaulieu',
                'prenom' => 'Valerie',
                'date_de_naissance' => Carbon::parse('1992-11-05'),
                'date_embauche' => Carbon::parse('2022-09-01 06:00:00+02'),
                'adresse_id' => null,
                'user_id' => 5,
                'matricule' => 'EMP2025-004',
            ],
            [
                'nom' => 'Tremblay',
                'prenom' => 'Elisabeth',
                'date_de_naissance' => Carbon::parse('1995-05-10'),
                'date_embauche' => Carbon::parse('2023-02-28 06:00:00+01'),
                'adresse_id' => null,
                'user_id' => 6,
                'matricule' => 'EMP2025-005',
            ],
        ];

        $date_debut = Carbon::now()->format('Y-m-d\TH:i');


        foreach ($employees as $employeeData) {
            $employe = Employe::create($employeeData);
            HistoriqueHeuresSemaines::create([
                'employe_id' => $employe->id,
                'nombre_d_heure_semaine' => 35,
                'date_debut' => $date_debut,
            ]);
        }
    }
}
