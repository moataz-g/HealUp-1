@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $repas->nom ?? 'Repas sans nom' }}</h1>
                        <p class="text-blue-100 mt-1">
                            @if($repas->type_repas == 'petit-dejeuner') 🌅 Petit-déjeuner
                            @elseif($repas->type_repas == 'dejeuner') ☀️ Déjeuner
                            @elseif($repas->type_repas == 'diner') 🌙 Dîner
                            @else 🍴 {{ str_replace('-', ' ', $repas->type_repas ?? 'Inconnu') }}
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold">{{ round($repas->calories_total ?? 0) }}</div>
                        <div class="text-sm text-blue-100">kcal</div>
                    </div>
                </div>
            </div>

            <!-- Informations générales -->
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Date de consommation:</span>
                        <div class="font-medium">
                            {{ $repas->date_consommation ? $repas->date_consommation->format('d/m/Y à H:i') : 'Non définie' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-600">Créé par:</span>
                        <div class="font-medium">{{ $repas->user->name ?? 'Utilisateur inconnu' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Créé le:</span>
                        <div class="font-medium">
                            {{ $repas->created_at ? $repas->created_at->format('d/m/Y à H:i') : 'Date inconnue' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations nutritionnelles -->
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold mb-4">Informations Nutritionnelles</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-red-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-red-600">{{ round($repas->calories_total ?? 0) }}</div>
                        <div class="text-sm text-red-500">Calories (kcal)</div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ round($repas->proteines_total ?? 0) }}g</div>
                        <div class="text-sm text-blue-500">Protéines</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-green-600">{{ round($repas->glucides_total ?? 0) }}g</div>
                        <div class="text-sm text-green-500">Glucides</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ round($repas->lipides_total ?? 0) }}g</div>
                        <div class="text-sm text-yellow-500">Lipides</div>
                    </div>
                </div>
            </div>

            <!-- Liste des ingrédients -->
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold mb-4">Ingrédients ({{ $repas->repasIngredients->count() }})</h3>
                
                @if($repas->repasIngredients && $repas->repasIngredients->count() > 0)
                    <div class="space-y-3">
                        @foreach($repas->repasIngredients as $repasIngredient)
                            @php
                                $ingredient = $repasIngredient->ingredient;
                                $quantite = $repasIngredient->quantite;
                            @endphp
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h4 class="font-medium text-gray-900">{{ $ingredient->nom ?? 'Ingrédient inconnu' }}</h4>
                                        <span class="ml-2 px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">
                                            {{ $ingredient->categorie ?? 'Non catégorisé' }}
                                        </span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Quantité: <span class="font-medium">{{ $quantite ?? 0 }}g</span>
                                    </div>
                                    @if($ingredient->allergenes)
                                        <div class="mt-1 text-xs text-orange-600">
                                            ⚠️ Allergènes: {{ $ingredient->allergenes }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ round($repasIngredient->calories_calculees ?? 0) }} kcal
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        P: {{ round($repasIngredient->proteines_calculees ?? 0) }}g | 
                                        G: {{ round($repasIngredient->glucides_calculees ?? 0) }}g | 
                                        L: {{ round($repasIngredient->lipides_calculees ?? 0) }}g
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 text-4xl mb-2">🍽️</div>
                        <p class="text-gray-500">Aucun ingrédient dans ce repas.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-4">
            <a href="{{ route('repas.edit', $repas->id) }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Modifier ce repas
            </a>
            
            <form action="{{ route('repas.destroy', $repas->id) }}" method="POST" class="inline" 
                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce repas ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Supprimer
                </button>
            </form>
            
            <a href="{{ route('repas.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Retour à la liste
            </a>
        </div>
    </div>
</div>
@endsection
