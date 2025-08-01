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
        Schema::create('annee_financieres', function (Blueprint $table) {
            $table->id();
            $table->date('debut')->comment('Date de début de l\'année financière (1er avril)');
            $table->date('fin')->comment('Date de fin de l\'année financière (31 mars)');
            $table->boolean('actif')->default(true)->comment('Année financière active (une seule à la fois)');
            
            // Index pour optimiser les requêtes
            $table->index('actif');
            $table->index('debut');
            $table->index('fin');       
            // Éviter les doublons
            $table->unique(['debut', 'fin']);        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annee_financieres');
    }
};
