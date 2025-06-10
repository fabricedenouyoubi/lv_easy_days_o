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
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->string('ville', 60)->nullable()->comment('Ville');
            $table->string('rue', 50)->nullable()->comment('Rue');
            $table->string('appartement', 10)->nullable()->comment('Numéro d\'appartement');
            $table->string('code_postal', 7)->nullable()->comment('Code postal');
            $table->string('telephone', 12)->nullable()->comment('Téléphone personnel');
            $table->string('telephone_pro', 12)->nullable()->comment('Téléphone professionnel');
            $table->string('telephone_pro_ext', 5)->nullable()->comment('Extension téléphone pro');
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index('code_postal');
            $table->index('ville');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
