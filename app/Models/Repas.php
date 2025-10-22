<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Repas extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'nom',
        'type_repas',
        'date_consommation',
        'user_id',  // ⭐ AJOUTEZ CECI
        'calories_total',
        'proteines_total',
        'glucides_total',
        'lipides_total'
    ];

    protected $casts = [
        'date_consommation' => 'datetime',
        'calories_total' => 'float',
        'proteines_total' => 'float',
        'glucides_total' => 'float',
        'lipides_total' => 'float'
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repasIngredients(): HasMany
    {
        return $this->hasMany(RepasIngredient::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'repas_ingredients')
            ->withPivot('quantite', 'calories_calculees')
            ->withTimestamps();
    }
}
