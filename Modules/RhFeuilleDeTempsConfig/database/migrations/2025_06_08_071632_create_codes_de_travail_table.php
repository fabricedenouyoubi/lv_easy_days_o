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
        Schema::create('codes_de_travail', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique()->comment('Identifiant unique du code');
            $table->string('libelle', 50)->comment('Nom du code de travail');
            $table->text('description')->nullable()->comment('Description détaillée');
            $table->enum('categorie', [
                'HEURE_REGULIERE',
                'HEURE_SUPPLEMENTAIRE', 
                'CONGE',
                'FORMATION',
                'DEPLACEMENT',
                'CAISSE_TEMPS',
                'CONGE_MOBILE',
                'CSN'
            ])->default('HEURE_REGULIERE')->comment('Catégorie du code');
            
            $table->index(['categorie']);
            $table->index(['code']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes_de_travail');
    }
};
