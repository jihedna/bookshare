@extends('layouts.modern')

@section('title', 'Accueil')

@section('content')
    <!-- Hero Section -->
    <section class="bg-primary text-white rounded-xl overflow-hidden mb-12">
        <div class="container mx-auto px-4 py-12 md:py-20">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">Partagez des Livres, Partagez le Savoir</h1>
                    <p class="text-xl mb-6">Rejoignez notre communauté de passionnés de lecture et partagez votre collection avec d'autres.</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('books.index') }}" class="btn bg-white text-primary hover:bg-gray-100">
                            <i class="ri-book-2-line mr-2"></i> Parcourir les Livres
                        </a>
                        <a href="{{ route('register') }}" class="btn bg-accent text-white hover:bg-accent/90">
                            <i class="ri-user-add-line mr-2"></i> Rejoindre
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <img src="{{ asset('images/hero-books.png') }}" alt="Collection de livres" class="max-w-full h-auto rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books -->
    <section class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Livres en Vedette</h2>
            <a href="{{ route('books.index') }}" class="text-primary hover:underline flex items-center">
                Voir Tous <i class="ri-arrow-right-line ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($featuredBooks ?? [] as $book)
                <x-book-card :book="$book" />
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
                    <i class="ri-book-2-line text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucun livre en vedette disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Categories -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Parcourir par Catégorie</h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($categories ?? [] as $category)
                <div class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition-all duration-200 hover:scale-105">
                    <div class="text-3xl text-primary mb-2">
                        <i class="ri-bookmark-line"></i>
                    </div>
                    <h3 class="font-medium">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $category->books_count ?? 0 }} livres</p>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
                    <i class="ri-folder-line text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Aucune catégorie disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- How It Works -->
    <section class="mb-12 bg-light rounded-xl p-8">
        <h2 class="text-2xl font-bold mb-8 text-center">Comment Fonctionne BookShare</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-primary/10 text-primary w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-user-add-line"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">1. Créez un Compte</h3>
                <p class="text-gray-600">Inscrivez-vous gratuitement et rejoignez notre communauté de passionnés de lecture.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-secondary/10 text-secondary w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-book-open-line"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">2. Ajoutez Vos Livres</h3>
                <p class="text-gray-600">Partagez votre collection avec les autres en ajoutant vos livres à la plateforme.</p>
            </div>
            
            <div class="text-center">
                <div class="bg-accent/10 text-accent w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="ri-exchange-line"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">3. Empruntez & Prêtez</h3>
                <p class="text-gray-600">Demandez des livres aux autres et approuvez les demandes pour vos propres livres.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Ce que Disent Nos Utilisateurs</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=Jean+Dupont&background=1E40AF&color=ffffff" 
                         alt="Jean Dupont" 
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h3 class="font-bold">Jean Dupont</h3>
                        <div class="flex text-accent">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"BookShare a transformé ma façon de lire. J'ai découvert tellement de nouveaux auteurs et genres grâce aux recommandations de la communauté."</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=Marie+Martin&background=10B981&color=ffffff" 
                         alt="Marie Martin" 
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h3 class="font-bold">Marie Martin</h3>
                        <div class="flex text-accent">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-half-fill"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"En tant que passionnée de lecture qui ne veut pas acheter tous les livres, BookShare est parfait. J'ai économisé de l'argent tout en profitant d'une grande variété de livres."</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <img src="https://ui-avatars.com/api/?name=Pierre+Dubois&background=F59E0B&color=ffffff" 
                         alt="Pierre Dubois" 
                         class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h3 class="font-bold">Pierre Dubois</h3>
                        <div class="flex text-accent">
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-fill"></i>
                            <i class="ri-star-line"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"La plateforme est facile à utiliser et la communauté est fantastique. J'ai rencontré d'autres passionnés de lecture dans ma région et nous avons maintenant des réunions régulières de club de lecture !"</p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-primary text-white rounded-xl p-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Prêt à Commencer à Partager ?</h2>
        <p class="text-xl mb-6 max-w-2xl mx-auto">Rejoignez des milliers de passionnés de lecture qui partagent déjà leurs collections et découvrent de nouvelles lectures.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}" class="btn bg-white text-primary hover:bg-gray-100">
                <i class="ri-user-add-line mr-2"></i> S'inscrire Maintenant
            </a>
            <a href="#" class="btn bg-transparent border border-white hover:bg-white/10">
                <i class="ri-information-line mr-2"></i> En Savoir Plus
            </a>
        </div>
    </section>
@endsection
