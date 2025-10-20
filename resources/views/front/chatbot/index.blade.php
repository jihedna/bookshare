@extends('layouts.modern')

@section('title', 'Chatbot')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Assistant BookShare</h1>
            <p class="text-gray-600 mt-2">Posez vos questions sur les livres, les auteurs ou obtenez des recommandations de lecture</p>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Historique des messages -->
            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4">
                <!-- Message de bienvenue -->
                <div class="flex">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white">
                            <i class="ri-robot-line text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                        <p class="text-gray-800">Bonjour ! Je suis l'assistant BookShare. Comment puis-je vous aider aujourd'hui ?</p>
                        <p class="text-gray-800 mt-2">Je peux vous aider à :</p>
                        <ul class="list-disc ml-5 text-gray-700 mt-1">
                            <li>Trouver des livres par genre ou thème</li>
                            <li>Obtenir des recommandations basées sur vos lectures précédentes</li>
                            <li>Répondre à vos questions sur les fonctionnalités de BookShare</li>
                        </ul>
                    </div>
                </div>

                <!-- Messages existants -->
                @if(isset($messages) && count($messages) > 0)
                    @foreach($messages as $message)
                        @if($message['sender'] === 'user')
                            <div class="flex justify-end">
                                <div class="bg-primary text-white rounded-lg p-3 max-w-[80%]">
                                    <p>{{ $message['content'] }}</p>
                                </div>
                                <div class="flex-shrink-0 ml-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="ri-user-line text-gray-600 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex">
                                <div class="flex-shrink-0 mr-3">
                                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white">
                                        <i class="ri-robot-line text-lg"></i>
                                    </div>
                                </div>
                                <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                                    <p class="text-gray-800">{{ $message['content'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            <!-- Zone de saisie -->
            <div class="border-t border-gray-200 p-4">
                <form id="chat-form" action="{{ route('chatbot.message') }}" method="POST" class="flex">
                    @csrf
                    <input type="text" name="message" id="message-input" placeholder="Posez votre question..."
                        class="input flex-grow mr-2" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-send-plane-fill mr-1"></i> Envoyer
                    </button>
                </form>
            </div>
        </div>

        <!-- Suggestions de questions -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Questions suggérées</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <button class="suggestion-btn text-left p-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors">
                    <i class="ri-question-line mr-2 text-primary"></i> Recommande-moi un livre de science-fiction
                </button>
                <button class="suggestion-btn text-left p-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors">
                    <i class="ri-question-line mr-2 text-primary"></i> Quels sont les livres les plus populaires ?
                </button>
                <button class="suggestion-btn text-left p-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors">
                    <i class="ri-question-line mr-2 text-primary"></i> Comment emprunter un livre ?
                </button>
                <button class="suggestion-btn text-left p-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-gray-700 transition-colors">
                    <i class="ri-question-line mr-2 text-primary"></i> Qui a écrit "Le Petit Prince" ?
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatMessages = document.getElementById('chat-messages');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const suggestionBtns = document.querySelectorAll('.suggestion-btn');
        
        // Faire défiler jusqu'au dernier message
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Gérer les suggestions de questions
        suggestionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const question = this.textContent.trim().replace(/^.*?\s/, '');
                messageInput.value = question;
                messageInput.focus();
            });
        });
        
        // Gérer l'envoi du formulaire avec AJAX
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Ajouter le message de l'utilisateur à l'interface
            const userMessageHtml = `
                <div class="flex justify-end">
                    <div class="bg-primary text-white rounded-lg p-3 max-w-[80%]">
                        <p>${message}</p>
                    </div>
                    <div class="flex-shrink-0 ml-3">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="ri-user-line text-gray-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            `;
            
            chatMessages.innerHTML += userMessageHtml;
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Afficher un indicateur de chargement
            const loadingHtml = `
                <div id="loading-message" class="flex">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white">
                            <i class="ri-robot-line text-lg"></i>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            `;
            
            chatMessages.innerHTML += loadingHtml;
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Envoyer la requête AJAX
            fetch(chatForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                // Supprimer l'indicateur de chargement
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.remove();
                }
                
                // Ajouter la réponse du chatbot
                const botMessageHtml = `
                    <div class="flex">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white">
                                <i class="ri-robot-line text-lg"></i>
                            </div>
                        </div>
                        <div class="bg-gray-100 rounded-lg p-3 max-w-[80%]">
                            <p class="text-gray-800">${data.response}</p>
                        </div>
                    </div>
                `;
                
                chatMessages.innerHTML += botMessageHtml;
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Réinitialiser le champ de saisie
                messageInput.value = '';
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Supprimer l'indicateur de chargement
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) {
                    loadingMessage.remove();
                }
                
                // Afficher un message d'erreur
                const errorMessageHtml = `
                    <div class="flex">
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center text-white">
                                <i class="ri-error-warning-line text-lg"></i>
                            </div>
                        </div>
                        <div class="bg-red-100 rounded-lg p-3 max-w-[80%]">
                            <p class="text-red-800">Désolé, une erreur s'est produite. Veuillez réessayer.</p>
                        </div>
                    </div>
                `;
                
                chatMessages.innerHTML += errorMessageHtml;
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        });
    });
</script>
@endpush
@endsection
