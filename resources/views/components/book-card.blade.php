@props(['book'])

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

            // Vérifier si l'utilisateur a déjà une demande d'emprunt en cours pour ce livre
            $hasActiveRequest = false;
            $requestStatus = '';
            if (auth()->check()) {
                try {
                    $user = auth()->user();
                    $loans = $book->getLoans();
                    
                    foreach ($loans as $loan) {
                        if ($loan->getBorrower()->getId() === auth()->id() && 
                            ($loan->getStatus() === 'REQUESTED' || ($loan->getStatus() === 'APPROVED' && $loan->getEndAt() === null))) {
                            $hasActiveRequest = true;
                            $requestStatus = $loan->getStatus();
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    $hasActiveRequest = false;
                }
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
            <span class="mx-2">•</span>
            <span class="flex items-center">
                <i class="ri-user-line mr-1"></i>
                {{ $book->getOwner()->getName() }}
            </span>
        </div>
        
        <p class="mt-3 text-sm line-clamp-3 text-gray-600">
            {{ $book->getDescription() ?? 'Aucune description disponible.' }}
        </p>
        
        <div class="mt-auto pt-4 flex justify-between items-center">
            <a href="{{ route('books.show', $book->getId()) }}" class="text-primary font-medium hover:underline">
                Voir Détails
            </a>
            
            @auth
                @if(auth()->id() === $book->getOwner()->getId())
                    <span class="text-xs text-gray-500 italic">Votre livre</span>
                @elseif($hasActiveRequest)
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                        @if($requestStatus === 'REQUESTED')
                            Demande en attente
                        @else
                            Déjà emprunté
                        @endif
                    </span>
                @elseif($book->isAvailable())
                    <form action="{{ route('loans.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->getId() }}">
                        <button type="submit" class="btn btn-accent text-sm">
                            <i class="ri-book-mark-line mr-1"></i> Emprunter
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-accent text-sm">
                    <i class="ri-login-box-line mr-1"></i> Connexion pour emprunter
                </a>
            @endauth
        </div>
    </div>
</div>
