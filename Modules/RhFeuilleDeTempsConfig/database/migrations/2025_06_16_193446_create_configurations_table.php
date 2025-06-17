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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('libelle', 200);
            $table->decimal('quota', 8, 2)->nullable()->default(0);
            $table->decimal('consomme', 8, 2)->nullable()->default(0);
            $table->decimal('reste', 8, 2)->nullable()->default(0);
            $table->date('date')->nullable();
            $table->text('commentaire')->nullable()->default('');
            
            // Relations 
            $table->foreignId('employe_id')->nullable()->constrained('employes')->onDelete('cascade');
            $table->foreignId('annee_budgetaire_id')->constrained('annee_financieres')->onDelete('cascade');
            $table->foreignId('code_travail_id')->constrained('codes_travail')->onDelete('cascade');
            
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['employe_id', 'annee_budgetaire_id', 'code_travail_id']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
