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
        Schema::create('historique_heures_semaines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->double('nombre_d_heure_semaine');
            $table->timestamp('date_debut')->default(now());
            $table->timestamp('date_fin')->nullable();

            $table->timestamps();

            // Clés étrangères
            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_heures_semaines');
    }
};
