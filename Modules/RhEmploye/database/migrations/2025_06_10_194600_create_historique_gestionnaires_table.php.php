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
        Schema::create('historique_gestionnaires', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employe_id');
            $table->unsignedBigInteger('gestionnaire_id');
            $table->timestamp('date_debut')->default(now());
            $table->timestamp('date_fin')->nullable();

            // Clés étrangères
            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
            $table->foreign('gestionnaire_id')->references('id')->on('employes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_gestionnaires');
    }
};
