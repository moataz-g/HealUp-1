@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mes Repas</h1>
        <a href="{{ route('repas.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Créer un nouveau repas
        </a>
    </div>

    @if($repas && $repas->count() > 0)
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nom du repas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Calories
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($repas as $repas_item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $repas_item->nom ?? 'Sans nom' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($repas_item->type_repas == 'petit-dejeuner') bg-yellow-100 text-yellow-800
                                @elseif($repas_item->type_repas == 'dejeuner') bg-green-100 text-green-800
                                @elseif($repas_item->type_repas == 'diner') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($repas_item->type_repas == 'petit-dejeuner') 🌅 Petit-déjeuner
                                @elseif($repas_item->type_repas == 'dejeuner') ☀️ Déjeuner
                                @elseif($repas_item->type_repas == 'diner') 🌙 Dîner
                                @else 🍴 {{ str_replace('-', ' ', $repas_item->type_repas ?? 'Inconnu') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $repas_item->date_consommation ? $repas_item->date_consommation->format('d/m/Y à H:i') : 'Non définie' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-medium">{{ round($repas_item->calories_total ?? 0) }} kcal</span>
                            <div class="text-xs text-gray-500">
                                {{ $repas_item->repasIngredients ? $repas_item->repasIngredients->count() : 0 }} ingrédient(s)
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('repas.show', $repas_item->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Voir
                            </a>
                            <a href="{{ route('repas.edit', $repas_item->id) }}" class="text-green-600 hover:text-green-900">
                                Modifier
                            </a>
                            <form action="{{ route('repas.destroy', $repas_item->id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce repas ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $repas->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 text-gray-400">
                🍽️
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun repas enregistré</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par enregistrer vos premiers repas.</p>
            <div class="mt-6">
                <a href="{{ route('repas.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Créer mon premier repas
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
