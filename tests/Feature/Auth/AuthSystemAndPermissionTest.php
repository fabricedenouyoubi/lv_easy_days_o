<?php

namespace Tests\Feature\Auth;

use App\Livewire\GestionUtilisateur;
use App\Livewire\GroupeForm;
use App\Livewire\GroupPermission;
use App\Livewire\PermissionUtilisateur;
use App\Models\User;
use Database\Seeders\GroupSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Modules\Roles\Database\Seeders\PermissionSeeder;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthSystemAndPermissionTest extends TestCase
{

    use RefreshDatabase;

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

        $this->assertDatabaseHas('roles', [
            'name' => 'RHS',
        ]);

        return Role::where('name', 'RHS')->first()->id;
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

        $this->assertDatabaseHas('roles', [
            'id' => $groupId,
            'name' => 'Updated Name',
        ]);
    }

    //------------------------------------------------------------------------------------------ PERMISSIONS GROUPES -----------------------------------------------------------------------------


    //--- test d'acces a la page des permissions de groupe
    public function test_can_mounts_with_group_permissions()
    {
        $role = Role::create(['name' => 'Admin']);
        $permission1 = Permission::create(['name' => 'voir utilisateurs', 'module' => 'Users']);
        $permission2 = Permission::create(['name' => 'modifier utilisateurs', 'module' => 'Users']);
        $role->givePermissionTo([$permission1, $permission2]);

        Livewire::test(GroupPermission::class, ['groupId' => $role->id])
            ->assertSet('checkedPermissions', $role->getPermissionNames());
    }

    //--- test de selection de permissions
    public function test_can_select_all_permissions()
    {
        $role = Role::create(['name' => 'Admin']);
        Permission::create(['name' => 'voir utilisateurs', 'module' => 'Users']);
        Permission::create(['name' => 'modifier utilisateurs', 'module' => 'Users']);

        $component = Livewire::test(GroupPermission::class, ['groupId' => $role->id]);

        $component->call('select_all');
        $component->assertSet('checkedPermissions', Permission::pluck('name')->toArray());
    }

    //--- test de deselection de permissions
    public function test_can_deselect_all_permissions()
    {
        $role = Role::create(['name' => 'Admin']);
        Permission::create(['name' => 'voir utilisateurs', 'module' => 'Users']);
        Permission::create(['name' => 'modifier utilisateurs', 'module' => 'Users']);

        $component = Livewire::test(GroupPermission::class, ['groupId' => $role->id]);

        $component->call('deselect_all');
        $component->assertSet('checkedPermissions', []);
    }

    //--- test de mise a jour des permissions de groupe et des permissions des utilisateurs
    public function test_can_set_group_permissions_and_update_users_permissions()
    {
        $this->seed(PermissionSeeder::class);

        $permissions = Permission::limit(2);
        $group = Role::findOrFail($this->insert_group());

        Livewire::test(GroupPermission::class, ['groupId' => $group->id])
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

    //--- test d'acces a la page des permissions d'un utilisateur
    public function test_can_see_user_permissions()
    {
        $role = Role::create(['name' => 'Éditeur']);
        $permission1 = Permission::create(['name' => 'publier article', 'module' => 'articles']);
        $permission2 = Permission::create(['name' => 'modifier article', 'module' => 'articles']);
        $role->givePermissionTo([$permission1, $permission2]);

        $user = User::factory()->create();
        $user->assignRole($role);

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->assertViewHas('permissions', function ($permissions) use ($permission1, $permission2) {
                return $permissions->contains($permission1) && $permissions->contains($permission2);
            });
    }

    //--- test de montage du composant avec les permissions de l'utilisateur
    /* public function test_can_mounts_with_user_permissions()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = User::first();
        $role = Role::create(['name' => 'SUPER ADMIN']);
        $permission1 = Permission::create(['name' => 'voir utilisateurs', 'module' => 'Users','guard_name' => 'web']);
        $permission2 = Permission::create(['name' => 'modifier utilisateurs', 'module' => 'Users', 'guard_name' => 'web']);
        $role->givePermissionTo([$permission1, $permission2]);
        $user->assignRole($role->name);

        Livewire::test(GroupPermission::class, ['groupId' => $role->id])
            ->assertSet('checkedPermissions', $user->permissions()->pluck('permission_id')->toArray());
     }*/

    //--- test de selection de permissions
    /* public function test_can_select_all_permissions_for_user()
    {
        $this->seed(UserSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::get();

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->call('select_all')
            ->assertSet('checkedPermissions', $permissions->pluck('id')->toArray());
    } */

    //--- test de deselection de permissions
    /* public function test_can_deselect_all_permissions_for_user()
    {
        $this->seed(UserSeeder::class);
        $this->seed(PermissionSeeder::class);

        $user = User::first();
        $permissions = Permission::limit(2)->get();
        $user->permissions()->sync($permissions->pluck('id'));

        Livewire::test(PermissionUtilisateur::class, ['userId' => $user->id])
            ->call('select_all')
            ->call('deselect_all')
            ->assertSet('checkedPermissions', []);
    } */

    //--- test de mise a jour des permissions de l'utilisateur
    /* public function test_can_set_user_permissions()
    {
        $this->seed(UserSeeder::class);
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
    } */
}
/*
    - commande de test :  php artisan test --filter=AuthSystemAndPermissionTest
*/
