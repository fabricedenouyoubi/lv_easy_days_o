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
        Schema::create('configuration_employe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuration_id')->constrained('configurations')->onDelete('cascade');
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            // Heures consommées par cet employé spécifiquement
            $table->decimal('consomme_individuel', 8, 2)->default(0); 
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['configuration_id', 'employe_id']);
            
            // Index pour améliorer les performances
            $table->index(['configuration_id']);
            $table->index(['employe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_employe');
    }
};
