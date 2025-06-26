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
            /* $table->id();
            $table->text('workflow_log')->nullable()->comment("Enregistre les changements de statut et les commentaires");

            $table->string('statut', 50)
                ->default('Brouillon');

            $table->timestamps(); // created_at & updated_at

            $table->decimal('total_heure', 4, 2)->nullable()->default(0)->comment("Total heures");
            $table->decimal('total_heure_deplacement', 4, 2)->nullable()->default(0)->comment("Heures déplacement");
            $table->decimal('total_heure_regulier', 4, 2)->nullable()->default(0)->comment("Heures régulières");
            $table->decimal('total_heure_supp', 4, 2)->nullable()->default(0)->comment("Heures supplémentaires");
            $table->decimal('total_heure_supp_ajuster', 4, 2)->nullable()->default(0)->comment("Heures supp. ajustées");
            $table->decimal('total_heure_formation', 4, 2)->nullable()->default(0)->comment("Heures formation");
            $table->decimal('total_heure_sup_a_payer', 4, 2)->nullable()->default(0)->comment("Heures supp. à payer");
            $table->decimal('total_heure_csn', 4, 2)->nullable()->default(0)->comment("Heures CSN");
            $table->decimal('total_heure_caisse', 4, 2)->nullable()->default(0)->comment("Heures caisse");
            $table->decimal('total_heure_conge_mobile', 4, 2)->nullable()->default(0)->comment("Heures congé mobile");

            // Foreign keys
            $table->foreignId('demande_d_absence_id')->nullable()->constrained('demandeabsence')->nullOnDelete();
            $table->foreignId('employe_id')->nullable()->constrained('employe')->nullOnDelete();
            $table->foreignId('feuille_de_temps_id')->nullable()->constrained('feuilledetemps')->nullOnDelete(); */
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
