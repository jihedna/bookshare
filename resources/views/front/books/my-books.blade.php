@extends('layouts.modern')

@section('title', 'Mes Livres')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mes Livres</h1>
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="ri-add-line mr-1"></i> Ajouter un livre
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="ri-checkbox-circle-line text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des livres -->
    @if(count($books) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="card h-full flex flex-col">
                    <div class="relative pb-[140%] bg-gray-100">
                        @php
                            $coverPath = null;
                            try {
                                if ($book && method_exists($book, 'getCoverPath')) {
                                    $coverPath = $book->getCoverPath();
                                }
                            } catch (\Exception $e) {
                                $coverPath = null;
                            }
                        @endphp

                        @if($coverPath)
                            <img src="{{ asset('storage/' . $coverPath) }}" 
                                alt="{{ $book->getTitle() }}" 
                                class="absolute inset-0 w-full h-full object-cover">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center bg-primary/10 text-primary">
                                <svg class="w-24 h-24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        @endif
                        
                        @if($book->isAvailable())
                            <span class="absolute top-2 right-2 bg-secondary text-white text-xs font-bold px-2 py-1 rounded-full">
                                Disponible
                            </span>
                        @else
                            <span class="absolute top-2 right-2 bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                Emprunté
                            </span>
                        @endif
                    </div>
                    
                    <div class="p-4 flex-grow flex flex-col">
                        <h3 class="font-bold text-lg line-clamp-2">{{ $book->getTitle() }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $book->getAuthor() }}</p>
                        
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <span class="flex items-center">
                                <i class="ri-book-open-line mr-1"></i>
                                @if($book->getCategories() && $book->getCategories()->count() > 0)
                                    {{ $book->getCategories()->first()->getName() }}
                                @else
                                    Non catégorisé
                                @endif
                            </span>
                        </div>
                        
                        <p class="mt-3 text-sm line-clamp-3 text-gray-600">
                            {{ $book->getDescription() ?? 'Aucune description disponible.' }}
                        </p>
                        
                        <div class="mt-auto pt-4 flex justify-between items-center">
                            <a href="{{ route('books.show', $book->getId()) }}" class="text-primary font-medium hover:underline">
                                Voir Détails
                            </a>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('books.edit', $book->getId()) }}" class="btn btn-sm btn-secondary">
                                    <i class="ri-edit-line mr-1"></i> Modifier
                                </a>
                                
                                <form action="{{ route('books.destroy', $book->getId()) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-gray-50 rounded-lg">
            <div class="text-primary text-5xl mb-4">
                <i class="ri-book-2-line"></i>
            </div>
            <h3 class="text-xl font-semibold mb-2">Vous n'avez pas encore ajouté de livres</h3>
            <p class="text-gray-500">Commencez à partager votre collection avec la communauté.</p>
            <a href="{{ route('books.create') }}" class="btn btn-primary mt-4">
                <i class="ri-add-line mr-1"></i> Ajouter un livre
            </a>
        </div>
    @endif
</div>
@endsection
