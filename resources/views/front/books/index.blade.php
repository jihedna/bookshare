@extends('layouts.modern')

@section('title', 'Livres')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Bibliothèque</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="ri-add-line mr-1"></i> Ajouter un livre
        </a>
    </div>

    <!-- Barre de recherche -->
    <div class="mb-8 max-w-xl mx-auto">
        <form action="{{ route('books.index') }}" method="GET" class="relative">
            <input type="text" name="query" value="{{ $query ?? '' }}" 
                placeholder="Rechercher par titre, auteur ou catégorie..." 
                class="input pl-10 pr-4 py-3 w-full shadow-sm">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <i class="ri-search-line text-xl"></i>
            </span>
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-primary hover:text-primary/80">
                <i class="ri-arrow-right-line text-xl"></i>
            </button>
        </form>
    </div>

    <!-- Filtres de catégories (à implémenter plus tard) -->
    
    <!-- Liste des livres -->
    @if(count($books) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{-- Pagination links if available --}}
        </div>
    @else
        <div class="text-center py-16 bg-gray-50 rounded-lg">
            <div class="text-primary text-5xl mb-4">
                <i class="ri-book-2-line"></i>
            </div>
            @if(isset($query))
                <h3 class="text-xl font-semibold mb-2">Aucun livre trouvé pour "{{ $query }}"</h3>
                <p class="text-gray-500">Essayez avec d'autres mots-clés ou parcourez tous les livres.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary mt-4">
                    Voir tous les livres
                </a>
            @else
                <h3 class="text-xl font-semibold mb-2">Aucun livre disponible</h3>
                <p class="text-gray-500">Soyez le premier à ajouter un livre à la bibliothèque.</p>
                <a href="{{ route('books.create') }}" class="btn btn-primary mt-4">
                    <i class="ri-add-line mr-1"></i> Ajouter un livre
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
