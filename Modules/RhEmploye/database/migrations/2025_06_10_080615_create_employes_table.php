<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 100)->unique()->nullable();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_de_naissance')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entreprise_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('gestionnaire_id')->nullable()->constrained('employes')->onDelete('set null');
            $table->integer('nombre_d_heure_semaine')->default(35);
            $table->foreignId('adresse_id')->nullable()->constrained('adresses')->onDelete('cascade');
            $table->dateTime('date_embauche')->nullable();
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
