<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repas_ingredients', function (Blueprint $table) {
            // Ajoutez seulement les colonnes qui manquent
            if (!Schema::hasColumn('repas_ingredients', 'repas_id')) {
                $table->unsignedBigInteger('repas_id')->after('id');
            }
            if (!Schema::hasColumn('repas_ingredients', 'ingredient_id')) {
                $table->unsignedBigInteger('ingredient_id')->after('repas_id');
            }
            if (!Schema::hasColumn('repas_ingredients', 'quantite')) {
                $table->float('quantite')->after('ingredient_id');
            }
            if (!Schema::hasColumn('repas_ingredients', 'calories_calculees')) {
                $table->float('calories_calculees')->after('quantite');
            }

            // Ajoutez les clés étrangères si elles n'existent pas
            try {
                $table->foreign('repas_id')->references('id')->on('repas')->onDelete('cascade');
                $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade');
            } catch (\Exception $e) {
                // Les clés étrangères existent déjà
            }
        });
    }

    public function down(): void
    {
        Schema::table('repas_ingredients', function (Blueprint $table) {
            $table->dropForeign(['repas_id']);
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn(['repas_id', 'ingredient_id', 'quantite', 'calories_calculees']);
        });
    }
};
