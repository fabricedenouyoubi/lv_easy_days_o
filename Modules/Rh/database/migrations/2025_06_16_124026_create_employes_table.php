<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Entreprise\Models\Adresse;
use Modules\Entreprise\Models\Entreprise;
use Modules\Rh\Models\Employe\Employe;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Adresse::class, 'adresse_id')->nullable();
            $table->foreignIdFor(Employe::class, 'gestionnaire_id')->nullable();
            $table->foreignIdFor(Entreprise::class, 'entreprise_id')->nullable();
            $table->foreignIdFor(User::class, 'user_id');
            $table->string('matricule');
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_de_naissance');
            $table->date('date_embauche');
            $table->integer('nombre_d_heure_semaine');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
