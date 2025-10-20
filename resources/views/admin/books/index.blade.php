@extends('admin.layout')

@section('title', 'Gestion des livres')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Gestion des livres</h1>
                <a href="{{ route('admin.books.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Ajouter un livre
                </a>
            </div>
            
            <div class="mb-6">
                <form action="{{ route('admin.books.index') }}" method="GET" class="flex">
                    <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Rechercher un livre..." class="flex-1 rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-r-md">
                        Rechercher
                    </button>
                </form>
            </div>
            
            @if(isset($query) && !empty($query))
                <div class="mb-4">
                    <p class="text-gray-600">Résultats de recherche pour: <span class="font-semibold">{{ $query }}</span></p>
                    <a href="{{ route('admin.books.index') }}" class="text-indigo-600 hover:text-indigo-900">Effacer la recherche</a>
                </div>
            @endif
            
            @if(count($books) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Titre</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Auteur</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Propriétaire</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Catégories</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date d'ajout</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($books as $book)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 mr-3 bg-gray-200 rounded">
                                                @if($book->getCoverPath())
                                                    <img src="{{ asset('storage/' . $book->getCoverPath()) }}" alt="{{ $book->getTitle() }}" class="h-10 w-10 object-cover rounded">
                                                @else
                                                    <svg class="h-10 w-10 text-gray-400 p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('books.show', $book->getId()) }}" class="text-indigo-600 hover:text-indigo-900 font-medium" target="_blank">
                                                    {{ $book->getTitle() }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $book->getAuthor() }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $book->getOwner()->getName() }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($book->getCategories() as $category)
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                                    {{ $category->getName() }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $book->getCreatedAt()->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.books.edit', $book->getId()) }}" class="text-blue-600 hover:text-blue-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.books.destroy', $book->getId()) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Aucun livre trouvé.</p>
            @endif
        </div>
    </div>
@endsection
