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
        Schema::create('lignes_travail', function (Blueprint $table) {
            $table->id();
           // Lundi (0)
            $table->time('debut_0')->nullable();
            $table->time('fin_0')->nullable();
            $table->decimal('duree_0', 4, 2)->nullable();
            
            // Mardi (1)
            $table->time('debut_1')->nullable();
            $table->time('fin_1')->nullable();
            $table->decimal('duree_1', 4, 2)->nullable();
            
            // Mercredi (2)
            $table->time('debut_2')->nullable();
            $table->time('fin_2')->nullable();
            $table->decimal('duree_2', 4, 2)->nullable();
            
            // Jeudi (3)
            $table->time('debut_3')->nullable();
            $table->time('fin_3')->nullable();
            $table->decimal('duree_3', 4, 2)->nullable();
            
            // Vendredi (4)
            $table->time('debut_4')->nullable();
            $table->time('fin_4')->nullable();
            $table->decimal('duree_4', 4, 2)->nullable();
            
            // Samedi (5)
            $table->time('debut_5')->nullable();
            $table->time('fin_5')->nullable();
            $table->decimal('duree_5', 4, 2)->nullable();
            
            // Dimanche (6)
            $table->time('debut_6')->nullable();
            $table->time('fin_6')->nullable();
            $table->decimal('duree_6', 4, 2)->nullable();

             // Champ pour marquer les lignes auto-remplies (absences, jours fériés)
            $table->boolean('auto_rempli')->default(false)->after('codes_travail_id');
            
            // Type d'auto-remplissage (absence, ferie, divers)
            $table->enum('type_auto_remplissage', ['absence', 'ferie', 'divers'])->nullable()->after('auto_rempli');         
            // Relations
            $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
            $table->foreignId('codes_travail_id')->constrained('codes_travail')->onDelete('cascade');
            
            
            $table->timestamps();
            
            // Index pour performance
            $table->index(['operation_id', 'codes_travail_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lignes_travail');
    }
};
