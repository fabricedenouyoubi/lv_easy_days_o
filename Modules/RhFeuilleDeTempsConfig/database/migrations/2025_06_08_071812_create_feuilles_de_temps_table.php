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
        Schema::create('feuilles_de_temps', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_semaine')->comment('Numéro de semaine dans l\'année');
            $table->date('debut')->comment('Date de début de semaine (dimanche)');
            $table->date('fin')->comment('Date de fin de semaine (samedi)');
            $table->foreignId('annee_financiere_id')
                  ->constrained('annee_financieres')
                  ->onDelete('cascade')
                  ->comment('Année financière associée');
            $table->boolean('actif')->default(false)->comment('Feuille active');
            $table->boolean('est_semaine_de_paie')->default(false)->comment('Semaine de paie');
            
            $table->index(['annee_financiere_id', 'numero_semaine']);
            $table->index(['debut', 'fin']);
            $table->index('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feuilles_de_temps');
    }
};
