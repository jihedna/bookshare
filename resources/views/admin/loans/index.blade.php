@extends('admin.layout')

@section('title', 'Gestion des emprunts')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Gestion des emprunts</h1>
            </div>
            
            <div class="mb-6">
                <div class="flex flex-wrap gap-2">
                    @foreach($statuses as $status => $label)
                        <a href="{{ route('admin.loans.index', ['status' => $status]) }}" 
                            class="px-4 py-2 rounded-md {{ $currentStatus === $status ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
            
            @php
                // Filter out loans with missing books or borrowers
                $validLoans = [];
                foreach($loans as $loan) {
                    try {
                        // Try to access the book and borrower to see if they exist
                        $book = $loan->getBook();
                        $borrower = $loan->getBorrower();
                        $bookTitle = $book->getTitle();
                        $borrowerName = $borrower->getName();
                        $owner = $book->getOwner();
                        $ownerName = $owner->getName();
                        
                        // If we got here without exceptions, the loan is valid
                        $validLoans[] = $loan;
                    } catch (\Exception $e) {
                        // Skip this loan if there's an error
                        continue;
                    }
                }
            @endphp
            
            @if(count($validLoans) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Livre</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Emprunteur</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Propriétaire</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Date de demande</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                                <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($validLoans as $loan)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 mr-3 bg-gray-200 rounded">
                                                @if($loan->getBook()->getCoverPath())
                                                    <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" alt="{{ $loan->getBook()->getTitle() }}" class="h-10 w-10 object-cover rounded">
                                                @else
                                                    <svg class="h-10 w-10 text-gray-400 p-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-indigo-600 hover:text-indigo-900 font-medium" target="_blank">
                                                    {{ $loan->getBook()->getTitle() }}
                                                </a>
                                                <p class="text-gray-500 text-sm">{{ $loan->getBook()->getAuthor() }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $loan->getBorrower()->getName() }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $loan->getBook()->getOwner()->getName() }}
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $loan->getStartAt()->format('d/m/Y') }}
                                    </td>
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
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.loans.show', $loan->getId()) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            
                                            @if($loan->getStatus() === 'REQUESTED')
                                                <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @elseif($loan->getStatus() === 'APPROVED')
                                                <form action="{{ route('admin.loans.update', $loan->getId()) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="action" value="return">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">Aucun emprunt trouvé.</p>
            @endif
        </div>
    </div>
@endsection
