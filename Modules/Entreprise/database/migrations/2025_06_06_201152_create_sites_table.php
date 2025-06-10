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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('Nom du site');
            $table->text('description')->nullable()->comment('Description du site');
            
            // Relations
            $table->foreignId('entreprise_id')
                  ->nullable()
                  ->constrained('entreprises')
                  ->onDelete('cascade')
                  ->comment('Référence vers l\'entreprise');
                  
            $table->foreignId('adresse_id')
                  ->nullable()
                  ->constrained('adresses')
                  ->onDelete('set null')
                  ->comment('Référence vers l\'adresse');
            
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index('name');
            $table->index('entreprise_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
