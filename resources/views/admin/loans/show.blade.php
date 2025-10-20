@extends('admin.layout')

@section('title', 'Détails de l\'emprunt')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Détails de l'emprunt</h1>
                <a href="{{ route('admin.loans.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    Retour à la liste
                </a>
            </div>
            
            @php
                $validBook = true;
                $validBorrower = true;
                $validOwner = true;
                $validCategories = true;
                
                try {
                    $book = $loan->getBook();
                    $bookTitle = $book->getTitle();
                    $bookAuthor = $book->getAuthor();
                    $bookCoverPath = $book->getCoverPath();
                } catch (\Exception $e) {
                    $validBook = false;
                }
                
                try {
                    $borrower = $loan->getBorrower();
                    $borrowerName = $borrower->getName();
                    $borrowerEmail = $borrower->getEmail();
                } catch (\Exception $e) {
                    $validBorrower = false;
                }
                
                if ($validBook) {
                    try {
                        $owner = $book->getOwner();
                        $ownerName = $owner->getName();
                    } catch (\Exception $e) {
                        $validOwner = false;
                    }
                    
                    try {
                        $categories = $book->getCategories();
                    } catch (\Exception $e) {
                        $validCategories = false;
                    }
                }
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">Informations sur le livre</h2>
                        
                        @if($validBook)
                            <div class="flex mb-4">
                                <div class="h-24 w-24 bg-gray-200 rounded flex-shrink-0 mr-4">
                                    @if($bookCoverPath)
                                        <img src="{{ asset('storage/' . $bookCoverPath) }}" alt="{{ $bookTitle }}" class="h-24 w-24 object-cover rounded">
                                    @else
                                        <svg class="h-24 w-24 text-gray-400 p-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">{{ $bookTitle }}</h3>
                                    <p class="text-gray-600">{{ $bookAuthor }}</p>
                                    <div class="mt-2">
                                        <a href="{{ route('books.show', $book->getId()) }}" class="text-indigo-600 hover:text-indigo-900 text-sm" target="_blank">
                                            Voir la page du livre
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                @if($validOwner)
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold">Propriétaire:</span> {{ $ownerName }}
                                    </p>
                                @endif
                                
                                @if($validCategories && count($categories) > 0)
                                    <div class="mt-2">
                                        <span class="text-sm font-semibold text-gray-600">Catégories:</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($categories as $category)
                                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                                    {{ $category->getName() }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <p>Le livre associé à cet emprunt n'existe plus ou est inaccessible.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">Informations sur l'emprunt</h2>
                        
                        <div class="space-y-3">
                            @if($validBorrower)
                                <div>
                                    <span class="font-semibold text-gray-700">Emprunteur:</span>
                                    <p>{{ $borrowerName }} ({{ $borrowerEmail }})</p>
                                </div>
                            @else
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                    <p>L'emprunteur associé à cet emprunt n'existe plus ou est inaccessible.</p>
                                </div>
                            @endif
                            
                            <div>
                                <span class="font-semibold text-gray-700">Date de demande:</span>
                                <p>{{ $loan->getStartAt()->format('d/m/Y à H:i') }}</p>
                            </div>
                            
                            @if($loan->getEndAt())
                                <div>
                                    <span class="font-semibold text-gray-700">Date de retour:</span>
                                    <p>{{ $loan->getEndAt()->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                            
                            <div>
                                <span class="font-semibold text-gray-700">Statut:</span>
                                @if($loan->getStatus() === 'REQUESTED')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">En attente</span>
                                @elseif($loan->getStatus() === 'APPROVED')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Approuvé</span>
                                @elseif($loan->getStatus() === 'REJECTED')
                                    <span class="bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded">Refusé</span>
                                @elseif($loan->getStatus() === 'RETURNED')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">Retourné</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($validBook && $validBorrower)
                            <div class="mt-6">
                                @if($loan->getStatus() === 'REQUESTED')
                                    <div class="flex space-x-2">
                                        <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                Approuver
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                                Refuser
                                            </button>
                                        </form>
                                    </div>
                                @elseif($loan->getStatus() === 'APPROVED')
                                    <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="return">
                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Marquer comme retourné
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
