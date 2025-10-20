@extends('layouts.modern')

@section('title', 'Mes Emprunts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Mes Emprunts</h1>
        <p class="text-gray-600 mt-2">Gérez vos demandes d'emprunt et les livres que vous avez empruntés</p>
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

    <!-- Onglets -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <a href="#borrowed" class="inline-block p-4 border-b-2 border-primary text-primary rounded-t-lg active" aria-current="page">
                    <i class="ri-book-read-line mr-1"></i> Mes Emprunts
                </a>
            </li>
            <li class="mr-2">
                <a href="#lent" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">
                    <i class="ri-book-open-line mr-1"></i> Mes Prêts
                </a>
            </li>
            <li class="mr-2">
                <a href="#requests" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">
                    <i class="ri-time-line mr-1"></i> Demandes en attente
                </a>
            </li>
            <li class="mr-2">
                <a href="#history" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">
                    <i class="ri-history-line mr-1"></i> Historique
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu des onglets -->
    <div id="borrowed" class="tab-content">
        <h2 class="text-xl font-semibold mb-4">Livres que j'ai empruntés</h2>
        
        @if(count($borrowedLoans ?? []) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriétaire</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunté le</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($borrowedLoans as $loan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-8 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                                @if($loan->getBook()->getCoverPath())
                                                    <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" 
                                                        alt="{{ $loan->getBook()->getTitle() }}" 
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                        <i class="ri-book-2-line"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-sm font-medium text-gray-900 hover:text-primary">
                                                    {{ $loan->getBook()->getTitle() }}
                                                </a>
                                                <div class="text-xs text-gray-500">
                                                    {{ $loan->getBook()->getAuthor() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $loan->getBook()->getOwner()->getName() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('loans.update', $loan->getId()) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="RETURNED">
                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                <i class="ri-arrow-go-back-line mr-1"></i> Retourner
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <div class="text-primary text-4xl mb-3">
                    <i class="ri-book-read-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Vous n'avez pas d'emprunts actifs</h3>
                <p class="text-gray-500">Parcourez la bibliothèque pour trouver des livres à emprunter.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary mt-4">
                    <i class="ri-book-2-line mr-1"></i> Parcourir les livres
                </a>
            </div>
        @endif
    </div>

    <!-- Mes Prêts -->
    <div id="lent" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">Livres que j'ai prêtés</h2>
        
        @if(count($lentLoans ?? []) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunteur</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($lentLoans as $loan)
                                @if($loan->getStatus() === 'APPROVED' && $loan->getEndAt() === null)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-8 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                                    @if($loan->getBook()->getCoverPath())
                                                        <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" 
                                                            alt="{{ $loan->getBook()->getTitle() }}" 
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <i class="ri-book-2-line"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-sm font-medium text-gray-900 hover:text-primary">
                                                        {{ $loan->getBook()->getTitle() }}
                                                    </a>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $loan->getBook()->getAuthor() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getBorrower()->getName() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <div class="text-primary text-4xl mb-3">
                    <i class="ri-book-open-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Vous n'avez pas de livres prêtés actuellement</h3>
                <p class="text-gray-500">Lorsque quelqu'un emprunte vos livres, ils apparaîtront ici.</p>
            </div>
        @endif
    </div>

    <!-- Demandes en attente -->
    <div id="requests" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">Demandes d'emprunt en attente</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Demandes que j'ai faites -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Mes demandes</h3>
                </div>
                
                @if(count($requestedLoans ?? []) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriétaire</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($requestedLoans as $loan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-8 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                                    @if($loan->getBook()->getCoverPath())
                                                        <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" 
                                                            alt="{{ $loan->getBook()->getTitle() }}" 
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <i class="ri-book-2-line"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-sm font-medium text-gray-900 hover:text-primary">
                                                        {{ $loan->getBook()->getTitle() }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getBook()->getOwner()->getName() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Vous n'avez pas de demandes d'emprunt en attente.</p>
                    </div>
                @endif
            </div>
            
            <!-- Demandes à approuver -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800">Demandes à approuver</h3>
                </div>
                
                @if(count($pendingApprovalLoans ?? []) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunteur</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pendingApprovalLoans as $loan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-8 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                                    @if($loan->getBook()->getCoverPath())
                                                        <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" 
                                                            alt="{{ $loan->getBook()->getTitle() }}" 
                                                            class="h-full w-full object-cover">
                                                    @else
                                                        <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                            <i class="ri-book-2-line"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-sm font-medium text-gray-900 hover:text-primary">
                                                        {{ $loan->getBook()->getTitle() }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getBorrower()->getName() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <form action="{{ route('loans.update', $loan->getId()) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="APPROVED">
                                                    <button type="submit" class="btn btn-sm bg-green-500 hover:bg-green-600 text-white">
                                                        <i class="ri-check-line mr-1"></i> Approuver
                                                    </button>
                                                </form>
                                                <form action="{{ route('loans.update', $loan->getId()) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="REJECTED">
                                                    <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white">
                                                        <i class="ri-close-line mr-1"></i> Refuser
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
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Vous n'avez pas de demandes à approuver.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div id="history" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">Historique des emprunts</h2>
        
        @if(count($historyLoans ?? []) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriétaire</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date d'emprunt</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de retour</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($historyLoans as $loan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-8 bg-gray-100 rounded overflow-hidden mr-3 flex-shrink-0">
                                                @if($loan->getBook()->getCoverPath())
                                                    <img src="{{ asset('storage/' . $loan->getBook()->getCoverPath()) }}" 
                                                        alt="{{ $loan->getBook()->getTitle() }}" 
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                                        <i class="ri-book-2-line"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('books.show', $loan->getBook()->getId()) }}" class="text-sm font-medium text-gray-900 hover:text-primary">
                                                    {{ $loan->getBook()->getTitle() }}
                                                </a>
                                                <div class="text-xs text-gray-500">
                                                    {{ $loan->getBook()->getAuthor() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $loan->getBook()->getOwner()->getName() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $loan->getStartAt()->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($loan->getEndAt())
                                                {{ $loan->getEndAt()->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($loan->getStatus() === 'RETURNED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Retourné
                                            </span>
                                        @elseif($loan->getStatus() === 'REJECTED')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Refusé
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $loan->getStatus() }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <div class="text-primary text-4xl mb-3">
                    <i class="ri-history-line"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun historique d'emprunt</h3>
                <p class="text-gray-500">Votre historique d'emprunts apparaîtra ici.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des onglets
        const tabs = document.querySelectorAll('ul li a');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Masquer tous les contenus d'onglets
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Réinitialiser tous les onglets
                tabs.forEach(t => {
                    t.classList.remove('border-primary', 'text-primary');
                    t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                });
                
                // Activer l'onglet cliqué
                this.classList.add('border-primary', 'text-primary');
                this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
                
                // Afficher le contenu correspondant
                const target = this.getAttribute('href').substring(1);
                document.getElementById(target).classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection
