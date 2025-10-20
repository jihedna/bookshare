@extends('layouts.modern')

@section('title', $book->getTitle())

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Fil d'Ariane -->
    <div class="text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-primary">Accueil</a>
        <span class="mx-2">/</span>
        <a href="{{ route('books.index') }}" class="hover:text-primary">Livres</a>
        <span class="mx-2">/</span>
        <span>{{ $book->getTitle() }}</span>
    </div>

    <!-- Alertes -->
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="md:flex">
            <!-- Image du livre -->
            <div class="md:w-1/3 bg-gray-100">
                <div class="aspect-[3/4] relative">
                    @if($book->getCoverPath())
                        <img src="{{ asset('storage/' . $book->getCoverPath()) }}" 
                            alt="{{ $book->getTitle() }}" 
                            class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-primary/10 text-primary p-8">
                            <img src="{{ asset('images/default-book-cover.svg') }}" alt="Couverture par défaut" class="w-full h-full">
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Détails du livre -->
            <div class="md:w-2/3 p-6 md:p-8">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $book->getTitle() }}</h1>
                        <p class="text-xl text-gray-600 mt-1">{{ $book->getAuthor() }}</p>
                    </div>
                    
                    @if(auth()->check() && auth()->id() == $book->getOwner()->getId())
                        <div class="flex space-x-2">
                            <a href="{{ route('books.edit', $book->getId()) }}" class="btn btn-secondary">
                                <i class="ri-edit-line mr-1"></i> Modifier
                            </a>
                            <form action="{{ route('books.destroy', $book->getId()) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-red-500 text-white hover:bg-red-600">
                                    <i class="ri-delete-bin-line mr-1"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
                
                <!-- Statut et propriétaire -->
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full {{ $book->isAvailable() ? 'bg-green-500' : 'bg-gray-500' }} mr-2"></span>
                        <span class="text-sm font-medium">{{ $book->isAvailable() ? 'Disponible' : 'Emprunté' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="ri-user-line text-gray-400 mr-2"></i>
                        <span class="text-sm">Propriétaire: <span class="font-medium">{{ $book->getOwner()->getName() }}</span></span>
                    </div>
                </div>
                
                <!-- Catégories -->
                <div class="mt-4">
                    <div class="flex flex-wrap gap-2">
                        @forelse($book->getCategories() as $category)
                            <span class="inline-block bg-primary/10 text-primary text-xs font-medium px-2.5 py-1 rounded-full">
                                {{ $category->getName() }}
                            </span>
                        @empty
                            <span class="text-sm text-gray-500">Aucune catégorie</span>
                        @endforelse
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Description</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        @if($book->getDescription())
                            <p>{{ $book->getDescription() }}</p>
                        @else
                            <p class="text-gray-500 italic">Aucune description disponible pour ce livre.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Résumé -->
                @if($book->getSummary())
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">Résumé</h2>
                        <div class="prose prose-sm max-w-none text-gray-600">
                            <p>{{ $book->getSummary() }}</p>
                        </div>
                    </div>
                @endif
                
                <!-- Actions -->
                <div class="mt-8">
                    @if(auth()->check() && $book->isAvailable() && auth()->id() != $book->getOwner()->getId())
                        <form action="{{ route('loans.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="book_id" value="{{ $book->getId() }}">
                            <button type="submit" class="btn btn-accent">
                                <i class="ri-book-mark-line mr-1"></i> Demander à emprunter
                            </button>
                        </form>
                    @elseif(!auth()->check())
                        <a href="{{ route('login') }}" class="btn btn-accent">
                            <i class="ri-login-box-line mr-1"></i> Connectez-vous pour emprunter
                        </a>
                    @elseif(!$book->isAvailable())
                        <button disabled class="btn bg-gray-300 text-gray-600 cursor-not-allowed">
                            <i class="ri-time-line mr-1"></i> Actuellement emprunté
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Livres similaires -->
    @if(count($similarBooks) > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Livres similaires</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($similarBooks as $similarBook)
                    <x-book-card :book="$similarBook" />
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
