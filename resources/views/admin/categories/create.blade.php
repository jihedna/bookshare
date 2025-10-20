@extends('admin.layout')

@section('title', 'Ajouter une catégorie')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Ajouter une catégorie</h1>
                <a href="{{ route('admin.categories.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    Retour à la liste
                </a>
            </div>
            
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="mb-4 max-w-md">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Ajouter la catégorie
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
