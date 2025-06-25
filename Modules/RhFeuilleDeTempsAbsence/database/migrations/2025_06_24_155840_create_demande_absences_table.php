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
        Schema::create('demande_absences', function (Blueprint $table) {
            $table->id();
            $table->text('workflow_log')->nullable();
            $table->enum('status', ['Brouillon', 'En cours', 'Soumis', 'Validé', 'Rejeté'])->default('Brouillon');
            $table->dateTime('date_debut')->nullable();
            $table->dateTime('date_fin')->nullable();
            $table->double('heure_par_jour')->default(8);
            $table->double('total_heure')->default(0);
            $table->text('description')->nullable();

            $table->foreignId('annee_financiere_id')->nullable()->constrained('annee_financieres')->nullOnDelete();
            $table->foreignId('codes_travail_id')->nullable()->constrained('codes_travail')->nullOnDelete();
            $table->foreignId('employe_id')->nullable()->constrained('employes')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_absences');
    }
};
