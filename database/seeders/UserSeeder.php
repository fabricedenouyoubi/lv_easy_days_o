<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //app()['cache']->forget('spatie.permission.cache');

        $user1 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => true,
            'email' => 'admin@mail.com',
            'is_staff' => true,
            'is_active' => true,
            'name' => 'Admin Plateforme GCS',
        ]);

        $user2 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => true,
            'email' => 'egravel@tcri.qc.ca',
            'is_staff' => true,
            'is_active' => true,
            'name' => 'Gravel Estelle',
        ]);

        $user3 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => false,
            'email' => 'eric@mail.com',
            'is_staff' => false,
            'is_active' => true,
            'name' => 'Duval Eric',
        ]);

        $user4 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => false,
            'email' => 'phillipe@mail.com',
            'is_staff' => false,
            'is_active' => true,
            'name' => 'Gagnon Phillipe',
        ]);

        $user5 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => false,
            'email' => 'valerie@mail.com',
            'is_staff' => false,
            'is_active' => true,
            'name' => 'Beaulieu Valerie',
        ]);

        $user6 = User::create([
            'password' => 'password',
            'last_login' => null,
            'is_superuser' => false,
            'email' => 'elisabeth@mail.com',
            'is_staff' => false,
            'is_active' => true,
            'name' => 'Tremblay Elisabeth',
        ]);

        $user1->assignRole('ADMIN');
        $user1->assignRole('GESTIONNAIRE');
    }
}
