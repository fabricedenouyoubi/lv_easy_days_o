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
        Schema::create('codes_travail', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('libelle', 150);
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
            $table->boolean('est_ajustable')->default(true)->comment('Indique si ce code doit être inclus dans le calcul du total des heures');
            $table->boolean('est_banque')->default(false)->comment('Indique si ce code entre dans la banque de temps d\'un employé');
            $table->boolean('cumule_banque')->default(false)->comment('Indique si ce code marque un solde d\'employé');
            $table->timestamps();           
            // Index pour améliorer les performances
            $table->index(['categorie_id']);
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes_travail');
    }
};
