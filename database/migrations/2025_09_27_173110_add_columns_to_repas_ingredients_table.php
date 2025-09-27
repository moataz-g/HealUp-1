<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repas_ingredients', function (Blueprint $table) {
            // Ajoutez les colonnes manquantes
            $table->unsignedBigInteger('repas_id')->after('id');
            $table->unsignedBigInteger('ingredient_id')->after('repas_id');
            $table->float('quantite')->after('ingredient_id'); // Quantité en grammes
            $table->float('calories_calculees')->after('quantite'); // Calories calculées
            
            // Ajoutez les clés étrangères
            $table->foreign('repas_id')->references('id')->on('repas')->onDelete('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            
            // Ajoutez un index pour optimiser les requêtes
            $table->index(['repas_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::table('repas_ingredients', function (Blueprint $table) {
            // Supprimez les clés étrangères d'abord
            $table->dropForeign(['repas_id']);
            $table->dropForeign(['ingredient_id']);
            
            // Supprimez l'index
            $table->dropIndex(['repas_id', 'ingredient_id']);
            
            // Supprimez les colonnes
            $table->dropColumn(['repas_id', 'ingredient_id', 'quantite', 'calories_calculees']);
        });
    }
};

