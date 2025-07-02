<?php

namespace Modules\RhFeuilleDeTempsAbsence\Tests\Feature;

use App\Models\User;
use Database\Seeders\EmployeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use Modules\Budget\Database\Seeders\BudgetDatabaseSeeder;
use Modules\Budget\Models\AnneeFinanciere;
use Modules\Entreprise\Database\Seeders\EntrepriseDatabaseSeeder;
use Modules\Rh\Livewire\Employe\EmployeForm;
use Modules\Rh\Models\Employe\Employe;
use Modules\RhFeuilleDeTempsAbsence\Livewire\RhFeuilleDeTempsAbsenceDetails;
use Modules\RhFeuilleDeTempsAbsence\Livewire\RhFeuilleDeTempsAbsenceForm;
use Modules\RhFeuilleDeTempsAbsence\Models\DemandeAbsence;
use Modules\RhFeuilleDeTempsConfig\Database\Seeders\RhFeuilleDeTempsConfigDatabaseSeeder;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;
use Modules\Roles\Database\Seeders\PermissionSeeder;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DemandeAbsenceTest extends TestCase
{
    use RefreshDatabase;

    //--- Test de connexion de l'utilisateur avant l'access a des pages
    public function connectUser()
    {
        $this->seed(PermissionSeeder::class);
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
    //--- test d'ajout d'un employe
    public function insert_employe()
    {
        $this->connectUser();

        $groupId = Role::first()->id;
        $groupName = Role::first()->name;

        Livewire::test(EmployeForm::class)
            ->set('nom', 'Doe')
            ->set('prenom', 'John')
            ->set('date_de_naissance', '1990-05-10')
            ->set('email', 'johndoe@example.com')
            ->set('nombre_d_heure_semaine', 30)
            ->set('est_gestionnaire', true)
            ->set('groups', [$groupName])
            ->call('save')
            ->assertHasNoErrors();

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

    //--- Test d'acces a la pages de la liste des employes
    public function test_can_see_demande_absence_list_page()
    {
        $this->connectUser();
        $this->seed(EmployeSeeder::class);
        $response = $this->get(route('absence.list'));
        $response->assertStatus(200);
    }

    /**
     * Vérifie que la validation échoue si les champs obligatoires ne sont pas remplis.
     */
    public function test_validation_fails_when_required_fields_missing()
    {
        Livewire::test(RhFeuilleDeTempsAbsenceForm::class)
            ->call('save')
            ->assertHasErrors([
                'date_debut' => 'required',
                'date_fin' => 'required',
                'heure_par_jour' => 'required',
                'code_de_travail_id' => 'required',
            ]);
    }


    /**
     * Vérifie que la méthode save crée une nouvelle demande d'absence avec des données valides.
     */

    public function insert_demande_absence()
    {
        $this->connectUser();

        $this->seed(BudgetDatabaseSeeder::class);
        $this->seed(RhFeuilleDeTempsConfigDatabaseSeeder::class);

        //$annee = AnneeFinanciere::where('actif', true);
        $codeTravail = CodeTravail::with('categorie')
            ->whereHas('categorie', function ($query) {
                $query->whereIn('intitule', ['Absence', 'Caisse']);
            })
            ->first();

        //dd($annee);

        Livewire::test(RhFeuilleDeTempsAbsenceForm::class)
            ->set('date_debut', now()->addDays(1)->toDateTimeString())
            ->set('date_fin', now()->addDays(3)->toDateTimeString())
            ->set('heure_par_jour', 8)
            ->set('description', 'Test d\'absence')
            ->set('code_de_travail_id', $codeTravail->id)
            ->call('save');

        $this->assertDatabaseHas('demande_absences', [
            'description' => 'Test d\'absence',
            'codes_travail_id' => $codeTravail->id,
        ]);

        return DemandeAbsence::first()->id;
    }

    public function test_save_creates_new_demande_absence()
    {
        $this->insert_demande_absence();
    }

    /**
     * Vérifie que la méthode cancel vide tous les champs du formulaire.
     */
    public function test_cancel_resets_form_fields()
    {
        $this->connectUser();

        $this->seed(BudgetDatabaseSeeder::class);
        $this->seed(RhFeuilleDeTempsConfigDatabaseSeeder::class);

        $codeTravail = $codeTravail = CodeTravail::with('categorie')
            ->whereHas('categorie', function ($query) {
                $query->whereIn('intitule', ['Absence', 'Caisse']);
            })
            ->first();

        Livewire::test(RhFeuilleDeTempsAbsenceForm::class)
            ->set('date_debut', now()->toDateTimeString())
            ->set('date_fin', now()->addDays(2)->toDateTimeString())
            ->set('heure_par_jour', 7)
            ->set('description', 'Test reset')
            ->set('code_de_travail_id', $codeTravail->id)
            ->call('cancel')
            ->assertSet('date_debut', null)
            ->assertSet('date_fin', null)
            ->assertSet('heure_par_jour', null)
            ->assertSet('description', null)
            ->assertSet('code_de_travail_id', null);
    }

    /**
     * Vérifie que resetAll recharge les valeurs de la demande d'absence existante.
     */
    public function test_resetAll_restores_original_values_from_existing_demande()
    {
        $demande = DemandeAbsence::findOrFail($this->insert_demande_absence());

        Livewire::test(RhFeuilleDeTempsAbsenceForm::class, [
            'demande_absence_id' => $demande->id
        ])
            ->call('resetAll')
            ->assertSet('description', $demande->description)
            ->assertSet('heure_par_jour', $demande->heure_par_jour);
    }

    /**
     * Vérifie que la demande d'absence est soumise avec succès.
     */
    public function test_can_submit_absence_request()
    {

        $demandeAbsence = DemandeAbsence::findOrFail($this->insert_demande_absence());

        // Teste la soumission de la demande d'absence et s'assure que le message de succès est bien affiché.
        Livewire::test(RhFeuilleDeTempsAbsenceDetails::class, ['demandeAbsenceId' => $demandeAbsence->id])
            ->call('soumettreDemandeAbsence')
            ->assertHasNoErrors();
    }

    /**
     * Vérifie que la demande d'absence peut être validée avec succès.
     */
    public function test_can_approve_absence_request()
    {
        $demandeAbsence = DemandeAbsence::findOrFail($this->insert_demande_absence());
        $demandeAbsence->update(['statut', 'Soumis']);
        // Teste l'approbation de la demande d'absence et vérifie si le message de succès est correctement affiché.
        Livewire::test(RhFeuilleDeTempsAbsenceDetails::class, ['demandeAbsenceId' => $demandeAbsence->id])
            ->call('approuverDemandeAbsence')
            ->assertHasNoErrors();
    }

    /**
     * Vérifie que la demande d'absence peut être rappelée avec succès.
     */
    public function test_can_recall_absence_request()
    {
        $demandeAbsence = DemandeAbsence::findOrFail($this->insert_demande_absence());
        $demandeAbsence->update(['statut', 'Validé']);

        // Teste la fonction de rappel de la demande d'absence
        Livewire::test(RhFeuilleDeTempsAbsenceDetails::class, ['demandeAbsenceId' => $demandeAbsence->id])
            ->set('motif', 'Motif de rappel') // Définir un motif pour le rappel
            ->call('rapelleDemandeAbsence') // Appel de la méthode de rappel
            ->assertHasNoErrors();
    }

    /**
     * Vérifie que la demande d'absence peut être retournée avec succès.
     */
    public function test_can_return_absence_request()
    {
        $demandeAbsence = DemandeAbsence::findOrFail($this->insert_demande_absence());
        $demandeAbsence->update(['statut', 'Soumis']);
        // Teste le retour de la demande d'absence avec un motif valide
        Livewire::test(RhFeuilleDeTempsAbsenceDetails::class, ['demandeAbsenceId' => $demandeAbsence->id])
            ->set('motif', 'Motif du retour') // Définir un motif
            ->call('retournerDemandeAbsence') // Appel de la méthode de retour
            ->assertHasNoErrors();
    }

    /**
     * Vérifie que la demande d'absence peut être rejetée avec succès.
     */
    public function test_can_reject_absence_request()
    {
        $demandeAbsence = DemandeAbsence::findOrFail($this->insert_demande_absence());
        $demandeAbsence->update(['statut', 'Soumis']);

        // Teste le rejet de la demande d'absence avec un motif valide
        Livewire::test(RhFeuilleDeTempsAbsenceDetails::class, ['demandeAbsenceId' => $demandeAbsence->id])
            ->set('motif', 'Motif du rejet') // Définir un motif pour le rejet
            ->call('rejeterDemandeAbsence') // Appel de la méthode de rejet
            ->assertHasNoErrors();
    }
}

/*
    - commnande de test
    - php artisan test Modules/RhFeuilleDeTempsAbsence/tests/Feature/DemandeAbsenceTest.php
 */
