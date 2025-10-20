@extends('admin.layout')

@section('title', 'Détails de l\'utilisateur')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Détails de l'utilisateur</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.users.edit', $user->getId()) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Modifier
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Retour
                    </a>
                </div>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Informations de base</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-500">ID:</span>
                                <span class="ml-2 text-gray-900">{{ $user->getId() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Nom:</span>
                                <span class="ml-2 text-gray-900">{{ $user->getName() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span class="ml-2 text-gray-900">{{ $user->getEmail() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Rôle:</span>
                                <span class="ml-2">
                                    @if($user->isAdmin())
                                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded">Admin</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">Utilisateur</span>
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Date d'inscription:</span>
                                <span class="ml-2 text-gray-900">{{ $user->getCreatedAt()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Statistiques</h2>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-500">Nombre de livres:</span>
                                <span class="ml-2 text-gray-900">{{ count($user->getBooks()) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Nombre d'emprunts:</span>
                                <span class="ml-2 text-gray-900">{{ count($user->getLoans()) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(count($user->getBooks()) > 0)
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Livres de l'utilisateur</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Titre</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Auteur</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date d'ajout</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($user->getBooks() as $book)
                                    <tr>
                                        <td class="py-3 px-4 text-gray-700">{{ $book->getTitle() }}</td>
                                        <td class="py-3 px-4 text-gray-700">{{ $book->getAuthor() }}</td>
                                        <td class="py-3 px-4 text-gray-700">{{ $book->getCreatedAt()->format('d/m/Y') }}</td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.books.show', $book->getId()) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
            
            @if(count($user->getLoans()) > 0)
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Emprunts de l'utilisateur</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Livre</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date de demande</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($user->getLoans() as $loan)
                                    <tr>
                                        <td class="py-3 px-4 text-gray-700">{{ $loan->getBook()->getTitle() }}</td>
                                        <td class="py-3 px-4 text-gray-700">{{ $loan->getStartAt()->format('d/m/Y') }}</td>
                                        <td class="py-3 px-4">
                                            @if($loan->getStatus() === 'REQUESTED')
                                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">En attente</span>
                                            @elseif($loan->getStatus() === 'APPROVED')
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Approuvé</span>
                                            @elseif($loan->getStatus() === 'REJECTED')
                                                <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Refusé</span>
                                            @elseif($loan->getStatus() === 'RETURNED')
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">Retourné</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('admin.loans.show', $loan->getId()) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
