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
        Schema::create('configuration_codes_de_travail', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->nullable()->comment('Libellé de la configuration');
            $table->foreignId('code_de_travail_id')
                  ->constrained('codes_de_travail')
                  ->onDelete('cascade')
                  ->comment('Code de travail associé');
            $table->foreignId('annee_financiere_id')
                  ->constrained('annee_financieres')
                  ->onDelete('cascade')
                  ->comment('Année financière');
            $table->decimal('nombre_d_heure', 8, 2)->default(0)->comment('Nombre d\'heures allouées');
            $table->date('jour')->nullable()->comment('Jour spécifique (pour jours fériés)');
            $table->decimal('nombre_d_heure_restant', 8, 2)->default(0)->comment('Heures restantes');
            $table->decimal('nombre_d_heure_pris', 8, 2)->default(0)->comment('Heures prises');
            $table->decimal('solde_heure_annee_precedente', 8, 2)->default(0)->comment('Solde année précédente');
            $table->decimal('quantite_heure_annee_courante', 8, 2)->default(0)->comment('Quantité année courante');
            $table->unsignedBigInteger('employe_id')->nullable()->comment('Employé (null pour config globale)');
            $table->text('description')->nullable()->comment('Description de la configuration');
            $table->date('debut')->nullable()->comment('Date de début validité');
            $table->date('fin')->nullable()->comment('Date de fin validité');
            
            $table->index(['annee_financiere_id', 'employe_id']);
            $table->index(['code_de_travail_id']);
            $table->index(['jour']);
            $table->timestamps();
            
            // TODO: Ajouter la foreign key pour employe_id quand le module RH sera créé
            // $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuration_codes_de_travail');
    }
};
