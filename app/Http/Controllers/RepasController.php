<?php

namespace App\Http\Controllers;

use App\Models\Repas;
use App\Models\Ingredient;
use App\Models\RepasIngredient;
use App\Services\AIRepasService;
use App\Services\AIAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepasController extends Controller
{
    public function index(Request $request)
    {
        $query = Repas::with(['repasIngredients.ingredient', 'user'])
            ->where('user_id', Auth::id() ?? 1);

        // 🔍 Recherche par nom
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhereHas('repasIngredients.ingredient', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        // 🍽️ Filtre par type de repas
        if ($request->filled('type_repas')) {
            $query->where('type_repas', $request->type_repas);
        }

        // 📅 Filtre par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_consommation', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_consommation', '<=', $request->date_fin);
        }

        // 🔥 Filtre par calories
        if ($request->filled('min_calories')) {
            $query->where('calories_total', '>=', $request->min_calories);
        }
        if ($request->filled('max_calories')) {
            $query->where('calories_total', '<=', $request->max_calories);
        }

        // 💪 Filtre par protéines
        if ($request->filled('min_proteines')) {
            $query->where('proteines_total', '>=', $request->min_proteines);
        }

        // 📊 Tri dynamique
        $sortField = $request->get('sort', 'date_consommation');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['nom', 'date_consommation', 'calories_total', 'proteines_total', 'glucides_total', 'lipides_total', 'type_repas'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $repas = $query->paginate(10)->withQueryString();

        // Données pour les filtres
        $typesRepas = ['petit-dejeuner', 'dejeuner', 'diner', 'collation'];

        return view('repas.index', compact('repas', 'typesRepas'));
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

        // Update
        $repas->nom = $validated['nom'];
        $repas->type_repas = $validated['type_repas'];
        $repas->date_consommation = $validated['date_consommation'];

        // Nutritional recalculations (same logic as store)
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

        // Delete old relationships and recreate
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

        return redirect()->route('repas.index')->with('success', 'Meal updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $repas = Repas::findOrFail($id);
            RepasIngredient::where('repas_id', $repas->id)->delete();
            $repas->delete();

            return redirect()->route('repas.index')->with('success', 'Meal deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('repas.index')->with('error', 'Error: ' . $e->getMessage());
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

        // Nutritional calculations
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

        // Create relationships
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

        return redirect()->route('repas.index')->with('success', 'Meal created successfully!');
    }

    /**
     * 🤖 Obtenir des suggestions IA de repas
     */
    public function aiSuggestions(Request $request, AIRepasService $aiService)
    {
        $user = Auth::user();

        $preferences = [
            'objectif_calories' => $request->input('objectif_calories', 2000),
            'objectif_proteines' => $request->input('objectif_proteines', 100),
            'restrictions' => $request->input('restrictions', [])
        ];

        $suggestions = $aiService->getSuggestions($user, $preferences);

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }

    /**
     * 📊 Analyser l'équilibre nutritionnel
     */
    public function analyzeBalance(Request $request, AIAnalysisService $analysisService)
    {
        $user = Auth::user();
        $periode = $request->input('periode', 7);

        $analysis = $analysisService->analyzeNutritionalBalance($user, $periode);

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    /**
     * ⚠️ Détecter les carences nutritionnelles
     */
    public function detectDeficiencies(Request $request, AIAnalysisService $analysisService)
    {
        $user = Auth::user();
        $periode = $request->input('periode', 14);

        $deficiencies = $analysisService->detectDeficiencies($user, $periode);

        return response()->json([
            'success' => true,
            'data' => $deficiencies
        ]);
    }

    /**
     * 📈 Générer un rapport nutritionnel
     */
    public function nutritionReport(Request $request, AIAnalysisService $analysisService)
    {
        $user = Auth::user();
        $periode = $request->input('periode', 30);

        $report = $analysisService->generateNutritionReport($user, $periode);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * 🎯 Prédire l'atteinte des objectifs
     */
    public function predictGoals(Request $request, AIAnalysisService $analysisService)
    {
        $user = Auth::user();

        $goals = $request->validate([
            'calories' => 'nullable|numeric|min:0',
            'proteines' => 'nullable|numeric',
            'glucides' => 'nullable|numeric',
            'lipides' => 'nullable|numeric'
        ]);

        $predictions = $analysisService->predictGoalAchievement($user, $goals);

        return response()->json([
            'success' => true,
            'predictions' => $predictions
        ]);
    }

    /**
     * ⚡ Optimiser un repas
     */
    public function optimizeRepas(Request $request, $id, AIRepasService $aiService)
    {
        $repas = Repas::with('repasIngredients.ingredient')->findOrFail($id);

        $targets = $request->validate([
            'calories' => 'nullable|numeric',
            'proteines' => 'nullable|numeric',
            'glucides' => 'nullable|numeric',
            'lipides' => 'nullable|numeric'
        ]);

        $optimizations = $aiService->optimizeRepas($repas, $targets);

        return response()->json([
            'success' => true,
            'optimizations' => $optimizations
        ]);
    }

    /**
     * 📅 Générer un plan hebdomadaire
     */
    public function weeklyPlan(Request $request, AIRepasService $aiService)
    {
        $user = Auth::user();

        $preferences = [
            'objectif_calories' => $request->input('objectif_calories', 2000),
            'nombre_repas' => $request->input('nombre_repas', 3),
            'restrictions' => $request->input('restrictions', [])
        ];

        $plan = $aiService->generateWeeklyMealPlan($user, $preferences);

        return response()->json([
            'success' => true,
            'plan' => $plan
        ]);
    }

    /**
     * ⭐ Analyser la qualité d'un repas
     */
    public function analyzeMealQuality($id, AIAnalysisService $analysisService)
    {
        $repas = Repas::with('repasIngredients.ingredient')->findOrFail($id);

        $quality = $analysisService->analyzeMealQuality($repas);

        return response()->json([
            'success' => true,
            'quality' => $quality
        ]);
    }
}
