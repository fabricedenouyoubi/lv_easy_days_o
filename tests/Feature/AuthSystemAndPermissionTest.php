<?php

namespace Tests\Feature;

use App\Livewire\GestionUtilisateur;
use App\Livewire\GroupeForm;
use App\Livewire\GroupPermission as LivewireGroupPermission;
use App\Livewire\LoginForm;
use App\Livewire\PermissionUtilisateur;
use App\Models\Group;
use App\Models\GroupPermission;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\ContentTypeSeeder;
use Database\Seeders\GroupSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class AuthSystemAndPermissionTest extends TestCase
{

    use RefreshDatabase;

    public function connectUser()
    {
        $this->seed(UserSeeder::class);

        $user = User::query()->where('email', 'admin@mail.com')->first();

        Livewire::test(LoginForm::class)
            ->set('email', 'admin@mail.com')
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        //--- Vérifie que l'utilisateur est authentifié
        $this->assertAuthenticatedAs($user);
    }

    //--- Test d'acces a la page de connexion
    public function test_can_see_login_page()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    //--- test de connexion de l'utilisateur
    public function test_can_login()
    {
        $this->connectUser();
    }

    //--- test du refus de connexion de l'utilisateur avec des données invalides
    public function test_cannot_login_with_invalid_data()
    {
        Livewire::test(LoginForm::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');

        $this->assertGuest();
    }

    //--- test de deconnexion de l'utilisateur
    public function test_can_logout()
    {
        $this->connectUser();

        $this->post(route('logout'));

        $this->assertGuest();
    }

    //--- test d'acces a la page des permissions
    public function test_can_see_permission_list_page()
    {
        $this->connectUser();
        $response = $this->get(route('permission.index'));
        $response->assertStatus(200);
    }

    //------------------------------------------------------------------------------------------ GROUPES -------------------------------------------------------------------------------------

    //--- test d'acces a la page des groupes
    public function test_can_see_group_list_page()
    {
        $this->connectUser();
        $response = $this->get(route('group.index'));
        $response->assertStatus(200);
    }

    public function insert_group()
    {
        Livewire::test(GroupeForm::class)
            ->set('name', 'RHS')
            ->call('save')
            ->assertDispatched('groupCreated');

        $this->assertDatabaseHas('groups', [
            'name' => 'RHS',
        ]);

        return Group::where('name', 'RHS')->first()->id;
    }
    //--- test d'ajout d'un groupe
    public function test_can_add_group()
    {
        $this->insert_group();
    }

    //--- test du refus d'ajoutou de modification d'un groupe avec des données invalides
    public function test_cannot_add_group_with_invalid_data()
    {
        Livewire::test(GroupeForm::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);

        Livewire::test(GroupeForm::class)
            ->set('name', 'AB')
            ->call('save')
            ->assertHasErrors(['name' => 'min']);
    }

    //--- test de modification d'un groupe
    public function test_can_update_group()
    {
        $groupId = $this->insert_group();


        Livewire::test(GroupeForm::class, ['groupId' => $groupId])
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertDispatched('groupUpdated');

        $this->assertDatabaseHas('groups', [
            'id' => $groupId,
            'name' => 'Updated Name',
        ]);
    }

    //------------------------------------------------------------------------------------------ PERMISSIONS GROUPES -----------------------------------------------------------------------------


    //--- test d'acces a la page des permissions de groupe
    public function test_can_mounts_with_group_permissions()
    {
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $group = Group::findOrFail($this->insert_group());
        $permission = Permission::first();

        $group->permissions()->attach([$permission->id]);

        Livewire::test(LivewireGroupPermission::class, ['groupId' => $group->id])
            ->assertSet('checkedPermissions', [$permission->id]);
    }

    //--- test de selection de permissions
    public function test_can_select_all_permissions()
    {
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $permissions = Permission::get();
        $group = $this->insert_group();

        Livewire::test(LivewireGroupPermission::class, ['groupId' => $group])
            ->call('select_all')
            ->assertSet('checkedPermissions', $permissions->pluck('id')->toArray());
    }

    //--- test de deselection de permissions
    public function test_can_deselect_all_permissions()
    {
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $group = Group::findOrFail($this->insert_group());
        $permissions = Permission::limit(3);
        $group->permissions()->sync($permissions->pluck('id'));

        Livewire::test(LivewireGroupPermission::class, ['groupId' => $group->id])
            ->call('select_all')
            ->call('deselect_all')
            ->assertSet('checkedPermissions', []);
    }

    //--- test de mise a jour des permissions de groupe et des permissions des utilisateurs
    public function test_can_set_group_permissions_and_update_users_permissions()
    {
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $permissions = Permission::limit(2);
        $group = Group::findOrFail($this->insert_group());

        Livewire::test(LivewireGroupPermission::class, ['groupId' => $group->id])
            ->set('checkedPermissions', $permissions->pluck('id')->toArray())
            ->call('set_group_permission')
            ->assertDispatched('groupPermissionUpdated', $group->name);
    }

    //------------------------------------------------------------------------------------------ PERMISSIONS UTILISATEURS -----------------------------------------------------------------------------

    //--- test d'acces a la page des utilisateurs
    public function test_can_see_users_list_page()
    {
        $this->connectUser();
        $response = $this->get(route('gestion_utilisateur.index'));
        $response->assertStatus(200);
    }

    //--- test de montage du composant avec les permissions de l'utilisateur
    public function test_can_mounts_with_user_permissions()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::limit(2)->get();
        $user->permissions()->sync($permissions->pluck('id')->toArray());

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->assertSet('checkedPermissions', $permissions->pluck('id')->toArray());
    }

    //--- test de selection de permissions
    public function test_can_select_all_permissions_for_user()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::get();

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->call('select_all')
            ->assertSet('checkedPermissions', $permissions->pluck('id')->toArray());
    }

    //--- test de deselection de permissions
    public function test_can_deselect_all_permissions_for_user()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::limit(2)->get();
        $user->permissions()->sync($permissions->pluck('id'));

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->call('select_all')
            ->call('deselect_all')
            ->assertSet('checkedPermissions', []);
    }

    //--- test de mise a jour des permissions de l'utilisateur
    public function test_can_set_user_permissions()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::limit(2)->get();

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->set('checkedPermissions', $permissions->pluck('id')->toArray())
            ->call('set_user_permission')
            ->assertDispatched('userPermissionUpdated', $user->name);

        $this->assertEqualsCanonicalizing(
            $permissions->pluck('id')->toArray(),
            $user->fresh()->permissions->pluck('id')->toArray()
        );
    }

    //--- test de reinitialisation des permissions d'un utilisateur en fonction de ses groupes
    public function test_can_reset_user_permissions_based_on_group()
    {
        $this->seed(UserSeeder::class);
        $this->seed(ContentTypeSeeder::class);
        $this->seed(PermissionSeeder::class);
        $this->seed(GroupSeeder::class);

        $user = User::first();
        $permissions = Permission::limit(3)->get();
        $group = Group::first();

        $group->permissions()->sync($permissions->pluck('id'));
        $user->groups()->attach($group);

        Livewire::test(GestionUtilisateur::class)
            ->call('reset_group_permission', $user->id);

        $this->assertEqualsCanonicalizing(
            $permissions->pluck('id')->toArray(),
            $user->fresh()->permissions->pluck('id')->toArray()
        );
    }
}
/*
    - commande de test :  php artisan test --filter=AuthSystemAndPermissionTest
*/
