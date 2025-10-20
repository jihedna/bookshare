@extends('layouts.modern')

@section('title', 'Modifier le profil')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Votre Profil</h1>
            <p class="text-gray-600 mt-2">Gérez vos informations personnelles et vos préférences</p>
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
                <!-- Menu latéral -->
                <div class="md:w-1/4 bg-gray-50 p-6 border-r border-gray-200">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-16 h-16 rounded-full bg-primary flex items-center justify-center text-white text-2xl">
                            {{ substr(auth()->user()->getName(), 0, 1) }}
                        </div>
                        <div>
                            <h2 class="font-semibold text-gray-800">{{ auth()->user()->getName() }}</h2>
                            <p class="text-sm text-gray-500">{{ auth()->user()->getEmail() }}</p>
                        </div>
                    </div>
                    
                    <nav class="space-y-1">
                        <a href="#profile" class="flex items-center px-3 py-2 text-sm font-medium rounded-md bg-primary text-white">
                            <i class="ri-user-line mr-3"></i>
                            Informations personnelles
                        </a>
                        <a href="#security" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                            <i class="ri-lock-line mr-3"></i>
                            Sécurité
                        </a>
                        <a href="#preferences" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                            <i class="ri-settings-line mr-3"></i>
                            Préférences
                        </a>
                    </nav>
                </div>
                
                <!-- Contenu principal -->
                <div class="md:w-3/4 p-6">
                    <!-- Informations personnelles -->
                    <div id="profile-section">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations personnelles</h2>
                        
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <!-- Nom -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->getName()) }}" 
                                    class="input w-full @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse e-mail</label>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->getEmail()) }}" 
                                    class="input w-full @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Bio (si applicable) -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                                <textarea name="bio" id="bio" rows="3" 
                                    class="input w-full @error('bio') border-red-500 @enderror">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
                                <p class="text-sm text-gray-500 mt-1">Parlez-nous un peu de vous et de vos goûts littéraires.</p>
                                @error('bio')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line mr-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Sécurité (masqué par défaut) -->
                    <div id="security-section" class="hidden">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Sécurité</h2>
                        
                        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <!-- Mot de passe actuel -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                                <input type="password" name="current_password" id="current_password" 
                                    class="input w-full @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Nouveau mot de passe -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                                <input type="password" name="password" id="password" 
                                    class="input w-full @error('password') border-red-500 @enderror">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Confirmation du mot de passe -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="input w-full">
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-lock-password-line mr-1"></i> Mettre à jour le mot de passe
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Préférences (masqué par défaut) -->
                    <div id="preferences-section" class="hidden">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Préférences</h2>
                        
                        <form action="{{ route('profile.preferences') }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            
                            <!-- Notifications -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-700 mb-2">Notifications</h3>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="notifications_email" id="notifications_email" 
                                            class="rounded text-primary focus:ring-primary" 
                                            {{ auth()->user()->notifications_email ?? false ? 'checked' : '' }}>
                                        <label for="notifications_email" class="ml-2 text-sm text-gray-700">
                                            Recevoir des notifications par e-mail
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="notifications_loans" id="notifications_loans" 
                                            class="rounded text-primary focus:ring-primary"
                                            {{ auth()->user()->notifications_loans ?? false ? 'checked' : '' }}>
                                        <label for="notifications_loans" class="ml-2 text-sm text-gray-700">
                                            Être notifié des demandes d'emprunt
                                        </label>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <input type="checkbox" name="notifications_returns" id="notifications_returns" 
                                            class="rounded text-primary focus:ring-primary"
                                            {{ auth()->user()->notifications_returns ?? false ? 'checked' : '' }}>
                                        <label for="notifications_returns" class="ml-2 text-sm text-gray-700">
                                            Être notifié des retours de livres
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Genres préférés -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-700 mb-2">Genres littéraires préférés</h3>
                                <p class="text-sm text-gray-500 mb-2">Sélectionnez vos genres préférés pour recevoir des recommandations personnalisées.</p>
                                
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @php
                                        $genres = ['Roman', 'Science-fiction', 'Fantasy', 'Policier', 'Biographie', 'Histoire', 'Philosophie', 'Poésie', 'Théâtre', 'Jeunesse'];
                                        $userPreferences = auth()->user()->genre_preferences ?? [];
                                    @endphp
                                    
                                    @foreach($genres as $genre)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="genre_preferences[]" id="genre-{{ $genre }}" 
                                                value="{{ $genre }}" 
                                                class="rounded text-primary focus:ring-primary"
                                                {{ in_array($genre, $userPreferences) ? 'checked' : '' }}>
                                            <label for="genre-{{ $genre }}" class="ml-2 text-sm text-gray-700">
                                                {{ $genre }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line mr-1"></i> Enregistrer les préférences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danger Zone -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6 border border-red-200">
            <h2 class="text-xl font-semibold text-red-600 mb-4">Zone de danger</h2>
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium text-gray-900">Supprimer mon compte</h3>
                    <p class="text-sm text-gray-500">Une fois votre compte supprimé, toutes vos ressources et données seront définitivement effacées.</p>
                </div>
                <button type="button" class="btn bg-red-500 text-white hover:bg-red-600" onclick="document.getElementById('delete-account-modal').classList.remove('hidden')">
                    <i class="ri-delete-bin-line mr-1"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de compte -->
<div id="delete-account-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Confirmer la suppression</h3>
        <p class="text-gray-700 mb-6">Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et toutes vos données seront définitivement supprimées.</p>
        
        <form action="{{ route('profile.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                <input type="password" name="password" id="password" required 
                    class="input w-full" placeholder="Entrez votre mot de passe pour confirmer">
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" class="btn bg-gray-100 text-gray-700 hover:bg-gray-200" onclick="document.getElementById('delete-account-modal').classList.add('hidden')">
                    Annuler
                </button>
                <button type="submit" class="btn bg-red-500 text-white hover:bg-red-600">
                    <i class="ri-delete-bin-line mr-1"></i> Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion des onglets
        const tabs = document.querySelectorAll('nav a');
        const sections = {
            'profile': document.getElementById('profile-section'),
            'security': document.getElementById('security-section'),
            'preferences': document.getElementById('preferences-section')
        };
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Masquer toutes les sections
                Object.values(sections).forEach(section => {
                    section.classList.add('hidden');
                });
                
                // Réinitialiser tous les onglets
                tabs.forEach(t => {
                    t.classList.remove('bg-primary', 'text-white');
                    t.classList.add('text-gray-600', 'hover:bg-gray-100', 'hover:text-gray-900');
                });
                
                // Activer l'onglet cliqué
                this.classList.add('bg-primary', 'text-white');
                this.classList.remove('text-gray-600', 'hover:bg-gray-100', 'hover:text-gray-900');
                
                // Afficher la section correspondante
                const target = this.getAttribute('href').substring(1);
                sections[target].classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection
