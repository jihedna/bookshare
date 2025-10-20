@extends('admin.layout')

@section('title', 'Modifier le livre')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Modifier le livre</h1>
                <a href="{{ route('admin.books.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    Retour à la liste
                </a>
            </div>
            
            <form action="{{ route('admin.books.update', $book->getId()) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $book->getTitle()) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('title')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Auteur</label>
                            <input type="text" name="author" id="author" value="{{ old('author', $book->getAuthor()) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('author')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-1">Propriétaire</label>
                            <select name="owner_id" id="owner_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Sélectionner un utilisateur</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->getId() }}" {{ old('owner_id', $book->getOwner()->getId()) == $user->getId() ? 'selected' : '' }}>
                                        {{ $user->getName() }} ({{ $user->getEmail() }})
                                    </option>
                                @endforeach
                            </select>
                            @error('owner_id')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Catégories</label>
                            <select name="categories[]" id="categories" multiple class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @foreach($categories as $category)
                                    <option value="{{ $category->getId() }}" 
                                        {{ in_array($category->getId(), old('categories', $book->getCategories()->map(function($cat) { return $cat->getId(); })->toArray())) ? 'selected' : '' }}>
                                        {{ $category->getName() }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-gray-500 text-xs mt-1">Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs catégories.</p>
                            @error('categories')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="cover" class="block text-sm font-medium text-gray-700 mb-1">Image de couverture</label>
                            
                            @if($book->getCoverPath())
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $book->getCoverPath()) }}" alt="{{ $book->getTitle() }}" class="h-40 object-cover rounded">
                                    <p class="text-sm text-gray-600 mt-1">Image actuelle</p>
                                </div>
                            @endif
                            
                            <input type="file" name="cover" id="cover" class="w-full">
                            <p class="text-gray-500 text-xs mt-1">Format accepté: JPG, PNG, GIF. Taille max: 2Mo. Laissez vide pour conserver l'image actuelle.</p>
                            @error('cover')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="8" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $book->getDescription()) }}</textarea>
                            @error('description')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        @if($book->getSummary())
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Résumé généré par IA</label>
                                <div class="p-3 bg-gray-50 rounded-md text-gray-700">
                                    {{ $book->getSummary() }}
                                </div>
                                <p class="text-gray-500 text-xs mt-1">Ce résumé est généré automatiquement et sera mis à jour si vous modifiez la description.</p>
                            </div>
                        @endif
                        
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Fonctionnalités AI</h3>
                            <p class="text-xs text-gray-600 mb-2">
                                Lorsque vous modifiez la description d'un livre, le système utilisera l'IA pour:
                            </p>
                            <ul class="text-xs text-gray-600 list-disc pl-5 space-y-1">
                                <li>Mettre à jour automatiquement le résumé</li>
                                <li>Suggérer des tags pertinents basés sur le contenu</li>
                                <li>Associer le livre aux catégories existantes correspondantes</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Mettre à jour le livre
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
