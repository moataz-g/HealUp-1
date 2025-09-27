<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repas;
use App\Models\Ingredient;
use App\Models\RepasIngredient;
use Illuminate\Support\Facades\Auth;

class RepasController extends Controller
{
    public function index()
    {
        $repas = Repas::with(['repasIngredients', 'user'])
            ->orderBy('date_consommation', 'desc')
            ->paginate(10);

        return view('repas.index', compact('repas'));
    }

    public function show($id)
    {
        $repas = Repas::with(['repasIngredients.ingredient', 'user'])->findOrFail($id);
        return view('repas.show', compact('repas'));
    }

    public function edit($id)
    {
        $repas = Repas::with(['repasIngredients.ingredient'])->findOrFail($id);
        $ingredients = Ingredient::orderBy('nom')->get();
        return view('repas.edit', compact('repas', 'ingredients'));
    }

    public function update(Request $request, $id)
    {
        $repas = Repas::findOrFail($id);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_repas' => 'required|in:petit-dejeuner,dejeuner,diner,collation',
            'date_consommation' => 'required|date',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|integer|exists:ingredients,id',
            'ingredients.*.quantite' => 'required|numeric|min:1|max:2000',
        ]);

        // Mise à jour
        $repas->nom = $validated['nom'];
        $repas->type_repas = $validated['type_repas'];
        $repas->date_consommation = $validated['date_consommation'];

        // Recalculs nutritionnels (même logique que store)
        $caloriesTotal = 0;
        $proteinesTotal = 0;
        $glucidesTotal = 0;
        $lipidesTotal = 0;

        foreach ($validated['ingredients'] as $ingData) {
            $ingredient = Ingredient::find($ingData['id']);
            $facteur = $ingData['quantite'] / 100;
            $caloriesTotal += $ingredient->calories_pour_100g * $facteur;
            $proteinesTotal += $ingredient->proteines_pour_100g * $facteur;
            $glucidesTotal += $ingredient->glucides_pour_100g * $facteur;
            $lipidesTotal += $ingredient->lipides_pour_100g * $facteur;
        }

        $repas->calories_total = $caloriesTotal;
        $repas->proteines_total = $proteinesTotal;
        $repas->glucides_total = $glucidesTotal;
        $repas->lipides_total = $lipidesTotal;
        $repas->save();

        // Supprimer anciennes relations et recréer
        RepasIngredient::where('repas_id', $repas->id)->delete();

        foreach ($validated['ingredients'] as $ingData) {
            $ingredient = Ingredient::find($ingData['id']);
            $facteur = $ingData['quantite'] / 100;

            RepasIngredient::create([
                'repas_id' => $repas->id,
                'ingredient_id' => $ingData['id'],
                'quantite' => $ingData['quantite'],
                'calories_calculees' => $ingredient->calories_pour_100g * $facteur,
                'proteines_calculees' => $ingredient->proteines_pour_100g * $facteur,
                'glucides_calculees' => $ingredient->glucides_pour_100g * $facteur,
                'lipides_calculees' => $ingredient->lipides_pour_100g * $facteur,
            ]);
        }

        return redirect()->route('repas.index')->with('success', 'Repas mis à jour!');
    }

    public function destroy($id)
    {
        try {
            $repas = Repas::findOrFail($id);
            RepasIngredient::where('repas_id', $repas->id)->delete();
            $repas->delete();
            
            return redirect()->route('repas.index')->with('success', 'Repas supprimé avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('repas.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $ingredients = Ingredient::orderBy('nom')->get();
        return view('repas.create', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_repas' => 'required|in:petit-dejeuner,dejeuner,diner,collation',
            'date_consommation' => 'required|date',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|integer|exists:ingredients,id',
            'ingredients.*.quantite' => 'required|numeric|min:1|max:2000',
        ]);

        $repas = new Repas();
        $repas->nom = $validated['nom'];
        $repas->type_repas = $validated['type_repas'];
        $repas->date_consommation = $validated['date_consommation'];
        $repas->user_id = Auth::id() ?? 1;

        // Calculs nutritionnels
        $caloriesTotal = 0;
        $proteinesTotal = 0;
        $glucidesTotal = 0;
        $lipidesTotal = 0;

        foreach ($validated['ingredients'] as $ingData) {
            $ingredient = Ingredient::find($ingData['id']);
            $facteur = $ingData['quantite'] / 100;
            $caloriesTotal += $ingredient->calories_pour_100g * $facteur;
            $proteinesTotal += $ingredient->proteines_pour_100g * $facteur;
            $glucidesTotal += $ingredient->glucides_pour_100g * $facteur;
            $lipidesTotal += $ingredient->lipides_pour_100g * $facteur;
        }

        $repas->calories_total = $caloriesTotal;
        $repas->proteines_total = $proteinesTotal;
        $repas->glucides_total = $glucidesTotal;
        $repas->lipides_total = $lipidesTotal;
        $repas->save();

        // Créer les relations
        foreach ($validated['ingredients'] as $ingData) {
            $ingredient = Ingredient::find($ingData['id']);
            $facteur = $ingData['quantite'] / 100;

            RepasIngredient::create([
                'repas_id' => $repas->id,
                'ingredient_id' => $ingData['id'],
                'quantite' => $ingData['quantite'],
                'calories_calculees' => $ingredient->calories_pour_100g * $facteur,
                'proteines_calculees' => $ingredient->proteines_pour_100g * $facteur,
                'glucides_calculees' => $ingredient->glucides_pour_100g * $facteur,
                'lipides_calculees' => $ingredient->lipides_pour_100g * $facteur,
            ]);
        }

        return redirect()->route('repas.index')->with('success', 'Repas créé!');
    }
}
