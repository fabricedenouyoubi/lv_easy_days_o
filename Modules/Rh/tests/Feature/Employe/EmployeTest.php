<?php

namespace Modules\Rh\Tests\Feature\Employe;

use App\Livewire\LoginForm;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\GroupSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Modules\Rh\Livewire\Employe\EmployeDetails;
use Modules\Rh\Livewire\Employe\EmployeEdit;
use Modules\Rh\Livewire\Employe\EmployeForm;
use Modules\Rh\Livewire\Employe\HistoriqueGestionnaireForm;
use Modules\Rh\Models\Employe\Employe;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;
use Tests\TestCase;

class EmployeTest extends TestCase
{
    use RefreshDatabase;

    //--- Test de connexion de l'utilisateur avant l'access a des pages
    public function connectUser()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::where('email', 'admin@mail.com')->first();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        return $user->id;
    }

    //--- Test d'acces a la pages de la liste des employes
    public function test_can_see_employe_list_page(): void
    {
        $this->connectUser();
        $response = $this->get(route('rh-employe.list'));
        $response->assertStatus(200);
    }

    public function insert_employe()
    {
        $this->connectUser();

        $this->seed(RoleSeeder::class);

        $groupId = ModelsRole::first()->id;
        $groupName = ModelsRole::first()->name;

        Livewire::test(EmployeForm::class)
            ->set('nom', 'Doe')
            ->set('prenom', 'John')
            ->set('date_de_naissance', '1990-05-10')
            ->set('email', 'johndoe@example.com')
            ->set('nombre_d_heure_semaine', 35)
            ->set('groups', [$groupName])
            ->call('save')
            ->assertDispatched('employeCreated');

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'name' => 'Doe John'
        ]);

        $this->assertDatabaseHas('employes', [
            'nom' => 'Doe',
            'prenom' => 'John',
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();
        $employeId = Employe::where('user_id', $user->id)->first()->id;

        $this->assertTrue($user->roles->contains('id', $groupId));

        return $employeId;
    }

    //--- test d'ajout d'un employe
    public function test_can_add_employe(): void
    {
        $this->insert_employe();
    }

    //--- test du refus d'ajouter un employe avec des données invalides
    public function test_cannot_add_employe_with_invalid_data()
    {
        Livewire::test(EmployeForm::class)
            ->set('nom', '')
            ->set('prenom', '')
            ->call('save')
            ->assertHasErrors(['nom', 'prenom', 'email']);
    }

    //--- test d'affichage des details d'un employe
    public function test_can_see_employe_details_page()
    {
        $employeId = $this->insert_employe();
        $response = $this->get(route('rh-employe.show', [$employeId]));
        $response->assertStatus(200);
    }

    //--- test de modification du mot de passe d'un employ
    public function test_can_change_employe_password()
    {
        $employeId = $this->insert_employe();
        $employe = Employe::findOrFail($employeId);
        $user = User::findOrFail($employe->user_id);

        Livewire::test(EmployeDetails::class, ['employeId' => $employeId])
            ->set('current_password', 'password')
            ->set('new_password', 'NouveauPass1!')
            ->set('new_password_confirmation', 'NouveauPass1!')
            ->call('changePassword')
            ->assertHasNoErrors();

        // Vérifie que le mot de passe a bien été mis à jour
        $this->assertTrue(Hash::check('NouveauPass1!', $user->fresh()->password));
    }

    //--- test du refus de modification du mot de passe d'un employ avec un mot de passe actuel incorrect
    public function test_cannot_change_password_with_invalid_current_password()
    {
        $employeId = $this->insert_employe();
        Livewire::test(EmployeDetails::class, ['employeId' => $employeId])
            ->set('current_password', 'MauvaisPass1!')
            ->set('new_password', 'NouveauPass1!')
            ->set('new_password_confirmation', 'NouveauPass1!')
            ->call('changePassword')
            ->assertHasErrors('current_password');
    }

    //--- test du refus de modification du mot de passe d'un employe avec des mots de passe invalides
    public function test_cannot_change_password_with_invalid_password()
    {
        $employeId = $this->insert_employe();

        Livewire::test(EmployeDetails::class, ['employeId' => $employeId])
            ->set('current_password', 'AncienPass1!')
            ->set('new_password', 'court')
            ->set('new_password_confirmation', 'court')
            ->call('changePassword')
            ->assertHasErrors('new_password'); // trop court, pas de symboles, etc.
    }

    //--- test de modification des details d'un employe
    public function test_can_update_employe_details()
    {

        $employe = Employe::findOrFail($this->insert_employe());
        $user = User::findOrFail($employe->user_id);

        $this->seed(RoleSeeder::class);
        $group = ModelsRole::first();


        Livewire::test(EmployeEdit::class, ['employeId' => $employe->id])
            ->set('nom', 'Nouveau')
            ->set('prenom', 'Prenom')
            ->set('email', 'nouveau@example.com')
            ->set('nombre_d_heure_semaine', 40)
            ->set('groups', [$group->id])
            ->call('save')
            ->assertDispatched('employeUpdated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'nouveau@example.com',
            'name' => 'Nouveau Prenom'
        ]);

        $this->assertDatabaseHas('employes', [
            'id' => $employe->id,
            'nom' => 'Nouveau',
            'prenom' => 'Prenom',
            'nombre_d_heure_semaine' => 40
        ]);
    }

    //--- test du refus de modification des details d'un employe avec des données invalides
    public function test_cannot_update_employe_with_invalid_data()
    {
        $employeId = $this->insert_employe();
        Livewire::test(EmployeEdit::class, ['employeId' => $employeId])
            ->set('nom', '')
            ->set('prenom', '')
            ->set('email', '')
            ->call('save')
            ->assertHasErrors(['nom', 'prenom', 'email']);
    }

    //--- test d'ajout d'un gestionnaire
    public function insert_gestionnaire()
    {
        $this->seed(RoleSeeder::class);

        $groupId = ModelsRole::where('name', 'GESTIONNAIRE')->first()->id;
        $groupName = ModelsRole::where('name', 'GESTIONNAIRE')->first()->name;

        Livewire::test(EmployeForm::class)
            ->set('nom', 'jegestion')
            ->set('prenom', 'John')
            ->set('date_de_naissance', '1990-05-10')
            ->set('email', 'jegestion@example.com')
            ->set('nombre_d_heure_semaine', 35)
            ->set('groups', [$groupName])
            ->call('save')
            ->assertDispatched('employeCreated');

        $this->assertDatabaseHas('users', [
            'email' => 'jegestion@example.com',
            'name' => 'jegestion John'
        ]);

        $this->assertDatabaseHas('employes', [
            'nom' => 'Doe',
            'prenom' => 'John',
        ]);

        $user = User::where('email', 'jegestion@example.com')->first();
        $gestionnaireId = Employe::where('user_id', $user->id)->first()->id;

        $this->assertTrue($user->roles->contains('id', $groupId));

        return $gestionnaireId;
    }

    //--- test d'ajout d'un historique gestionnaire
    public function test_can_add_gestionaire_hitstorique()
    {
        $employeId = $this->insert_employe();
        $gestionnaireId = $this->insert_gestionnaire();
        $dateDebut = Carbon::now()->format('Y-m-d\TH:i');

        Livewire::test(HistoriqueGestionnaireForm::class, [
            'employeId' => $employeId
        ])
            ->set('gestionnaire', $gestionnaireId)
            ->set('dateDebut', $dateDebut)
            ->call('saveHist')
            ->assertDispatched('gestionnaireAjoute');


        $this->assertDatabaseHas('employes', [
            'id' => $employeId,
            'gestionnaire_id' => $gestionnaireId,
        ]);

        $this->assertDatabaseHas('historique_gestionnaires', [
            'employe_id' => $employeId,
            'gestionnaire_id' => $gestionnaireId,
            'date_debut' => $dateDebut,
        ]);
    }
}
/*
    - commnande de test
    - php artisan test Modules/Rh/tests/Feature/Employe/EmployeTest.php
 */
