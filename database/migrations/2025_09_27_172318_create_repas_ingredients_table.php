<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repas_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('repas_id');
            $table->unsignedBigInteger('ingredient_id');
            $table->float('quantite'); // Quantité en grammes
            $table->float('calories_calculees'); // Calories calculées pour cette quantité
            $table->timestamps();

            // Clés étrangères
            $table->foreign('repas_id')->references('id')->on('repas')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            
            // Index pour optimiser les requêtes
            $table->index(['repas_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repas_ingredients');
    }
};
