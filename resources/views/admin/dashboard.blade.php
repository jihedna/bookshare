@extends('admin.layout')

@section('title', 'Tableau de bord')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Livres</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\Book::class)->count([]) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.books.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Voir tous les livres →
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Catégories</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\Category::class)->count([]) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.categories.index') }}" class="text-green-600 hover:text-green-900 text-sm font-medium">
                    Voir toutes les catégories →
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Emprunts en attente</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\Loan::class)->count(['status' => 'REQUESTED']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.loans.index', ['status' => 'REQUESTED']) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                    Voir les emprunts en attente →
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-700">Utilisateurs</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\User::class)->count([]) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                    Gestion des utilisateurs →
                </a>
            </div>
        </div>
    </div>
    
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Emprunts récents</h3>
            
            @php
                try {
                    $recentLoans = \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\Loan::class)
                        ->findBy([], ['startAt' => 'DESC'], 5);
                    
                    // Filter out loans with missing books or borrowers
                    $validLoans = [];
                    foreach ($recentLoans as $loan) {
                        try {
                            // Try to access the book and borrower to see if they exist
                            $book = $loan->getBook();
                            $borrower = $loan->getBorrower();
                            $bookTitle = $book->getTitle();
                            $borrowerName = $borrower->getName();
                            
                            // If we got here without exceptions, the loan is valid
                            $validLoans[] = $loan;
                        } catch (\Exception $e) {
                            // Skip this loan if there's an error
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    $validLoans = [];
                }
            @endphp
            
            @if(count($validLoans) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunteur</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($validLoans as $loan)
                                <tr>
                                    <td class="py-2 px-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $loan->getBook()->getTitle() }}</div>
                                    </td>
                                    <td class="py-2 px-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $loan->getBorrower()->getName() }}</div>
                                    </td>
                                    <td class="py-2 px-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="py-2 px-3 whitespace-nowrap">
                                        @if($loan->getStatus() === 'REQUESTED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                En attente
                                            </span>
                                        @elseif($loan->getStatus() === 'APPROVED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Approuvé
                                            </span>
                                        @elseif($loan->getStatus() === 'REJECTED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Refusé
                                            </span>
                                        @elseif($loan->getStatus() === 'RETURNED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Retourné
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Aucun emprunt récent.</p>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('admin.loans.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Voir tous les emprunts →
                </a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Livres récemment ajoutés</h3>
            
            @php
                try {
                    $recentBooks = \LaravelDoctrine\ORM\Facades\EntityManager::getRepository(\App\Domain\Entities\Book::class)
                        ->findBy([], ['createdAt' => 'DESC'], 5);
                } catch (\Exception $e) {
                    $recentBooks = [];
                }
            @endphp
            
            @if(count($recentBooks) > 0)
                <div class="space-y-4">
                    @foreach($recentBooks as $book)
                        @php
                            try {
                                $bookTitle = $book->getTitle();
                                $bookAuthor = $book->getAuthor();
                                $bookCreatedAt = $book->getCreatedAt();
                                $validBook = true;
                            } catch (\Exception $e) {
                                $validBook = false;
                            }
                        @endphp
                        
                        @if($validBook)
                            <div class="flex items-center">
                                <div class="h-12 w-12 bg-gray-200 rounded flex-shrink-0">
                                    @if($book->getCoverPath())
                                        <img src="{{ asset('storage/' . $book->getCoverPath()) }}" alt="{{ $bookTitle }}" class="h-12 w-12 object-cover rounded">
                                    @else
                                        <svg class="h-12 w-12 text-gray-400 p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $bookTitle }}</h4>
                                    <p class="text-xs text-gray-500">{{ $bookAuthor }}</p>
                                    <p class="text-xs text-gray-400">Ajouté le {{ $bookCreatedAt->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Aucun livre récent.</p>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('admin.books.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                    Voir tous les livres →
                </a>
            </div>
        </div>
    </div>
@endsection
