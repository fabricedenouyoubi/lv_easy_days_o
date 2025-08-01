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
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nom de l\'entreprise');
            $table->text('description')->nullable()->comment('Description de l\'entreprise');
            $table->integer('premier_jour_semaine')->default(1)->comment('Premier jour de la semaine (1=Lundi, 2=Mardi, ..., 7=Dimanche)');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
