@extends('front.layout')

@section('title', 'Résultat de la reconnaissance')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Résultat de la reconnaissance</h1>
                <a href="{{ route('book-recognition.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    Nouvelle analyse
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <img src="{{ $imageData }}" alt="Image du livre" class="w-full h-auto object-contain rounded-lg">
                    </div>
                    
                    @if(isset($extractedText))
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <h3 class="text-lg font-semibold mb-2">Texte extrait de l'image</h3>
                            <p class="text-gray-700">{{ $extractedText }}</p>
                            
                            @if(isset($searchQuery))
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Requête de recherche: {{ $searchQuery }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                
                <div>
                    @if(isset($bookInfo['error']))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <p>{{ $bookInfo['error'] }}</p>
                        </div>
                        
                        <p class="text-gray-600 mb-4">
                            Désolé, nous n'avons pas pu identifier le livre à partir de cette image. 
                            Veuillez essayer avec une autre image ou ajouter le livre manuellement.
                        </p>
                        
                        <div class="mt-4">
                            <a href="{{ route('books.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter un livre manuellement
                            </a>
                        </div>
                    @else
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            <p>Livre identifié avec succès !</p>
                            @if(isset($bookInfo['source']))
                                <p class="text-sm">Source: {{ $bookInfo['source'] }}</p>
                            @endif
                        </div>
                        
                        @if(isset($bookInfo['thumbnail']))
                            <div class="mb-4">
                                <img src="{{ $bookInfo['thumbnail'] }}" alt="{{ $bookInfo['title'] }}" class="h-48 object-contain">
                            </div>
                        @endif
                        
                        <form action="{{ route('book-recognition.create-book') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                                <input type="text" name="title" id="title" value="{{ $bookInfo['title'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="author" class="block text-sm font-medium text-gray-700">Auteur</label>
                                <input type="text" name="author" id="author" value="{{ $bookInfo['author'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ $bookInfo['description'] ?? '' }}</textarea>
                            </div>
                            
                            @if(isset($bookInfo['isbn']))
                                <div>
                                    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
                                    <input type="text" name="isbn" id="isbn" value="{{ $bookInfo['isbn'] }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" readonly>
                                </div>
                            @endif
                            
                            <div class="pt-4">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Continuer l'ajout du livre
                                </button>
                            </div>
                        </form>
                        
                        @if(isset($bookInfo['raw_response']))
                            <div class="mt-6">
                                <details>
                                    <summary class="text-sm text-gray-500 cursor-pointer">Voir la réponse complète</summary>
                                    <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                        <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ $bookInfo['raw_response'] }}</pre>
                                    </div>
                                </details>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
