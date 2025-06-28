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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->text('workflow_log')->nullable();

            $table->string('statut', 50)
                ->default('Brouillon');

            $table->timestamps();

            $table->double('total_heure')->nullable()->default(0)->comment("Total heures");
            $table->double('total_heure_deplacement')->nullable()->default(0)->comment("Heures déplacement");
            $table->double('total_heure_regulier')->nullable()->default(0)->comment("Heures régulières");
            $table->double('total_heure_supp')->nullable()->default(0)->comment("Heures supplémentaires");
            $table->double('total_heure_supp_ajuster')->nullable()->default(0)->comment("Heures supp. ajustées");
            $table->double('total_heure_formation')->nullable()->default(0)->comment("Heures formation");
            $table->double('total_heure_sup_a_payer')->nullable()->default(0)->comment("Heures supp. à payer");
            $table->double('total_heure_csn')->nullable()->default(0)->comment("Heures CSN");
            $table->double('total_heure_caisse')->nullable()->default(0)->comment("Heures caisse");
            $table->double('total_heure_conge_mobile')->nullable()->default(0)->comment("Heures congé mobile");

            // Colonne pour la gestion du workflow
            $table->string('workflow_state', 50)->default('brouillon')->after('statut');

            // Foreign keys
            $table->foreignId('demande_absence_id')->nullable()->constrained('demande_absences')->nullOnDelete();
            $table->foreignId('employe_id')->nullable()->constrained('employes')->nullOnDelete();
            $table->foreignId('annee_semaine_id')->nullable()->constrained('annee_semaines')->nullOnDelete();

            // Indexation
            $table->index(['workflow_state']);
            $table->index(['employe_id', 'workflow_state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
