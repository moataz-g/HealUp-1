<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('nom')->nullable()->after('id');
            $table->string('categorie', 100)->nullable()->after('nom');
            $table->decimal('calories_pour_100g', 8, 2)->nullable()->after('categorie');
            $table->decimal('proteines_pour_100g', 8, 2)->nullable()->after('calories_pour_100g');
            $table->decimal('glucides_pour_100g', 8, 2)->nullable()->after('proteines_pour_100g');
            $table->decimal('lipides_pour_100g', 8, 2)->nullable()->after('glucides_pour_100g');
            $table->decimal('fibres_pour_100g', 8, 2)->nullable()->after('lipides_pour_100g');
            $table->json('allergenes')->nullable()->after('fibres_pour_100g');
            $table->string('image')->nullable()->after('allergenes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'nom',
                'categorie',
                'calories_pour_100g',
                'proteines_pour_100g',
                'glucides_pour_100g',
                'lipides_pour_100g',
                'fibres_pour_100g',
                'allergenes',
                'image'
            ]);
        });
    }
};
