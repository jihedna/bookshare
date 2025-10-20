<?php

namespace App\Services;

class SimpleChatbotService
{
    /**
     * Get a response from the chatbot
     *
     * @param string $message User message
     * @param array $context Additional context
     * @return string Response message
     */
    public function getChatbotResponse(string $message, array $context = []): string
    {
        $message = strtolower(trim($message));
        
        // Greetings
        if (preg_match('/(bonjour|salut|hello|hi|hey|coucou)/i', $message)) {
            return $this->getRandomResponse([
                'Bonjour ! Comment puis-je vous aider avec BookShare aujourd\'hui ?',
                'Salut ! Que puis-je faire pour vous ?',
                'Bonjour ! Je suis BookBot, votre assistant pour BookShare. Comment puis-je vous aider ?'
            ]);
        }
        
        // Book recommendations
        if (preg_match('/(recommend|suggestion|propose|conseil|suggère|idée|recommande)/i', $message)) {
            return $this->getRandomResponse([
                'Je vous recommande de découvrir nos livres les plus populaires dans la section principale.',
                'Avez-vous essayé de filtrer par catégorie ? Nous avons une excellente sélection de romans, essais et livres techniques.',
                'Basé sur l\'activité récente, les livres de science-fiction et de développement personnel sont très appréciés en ce moment.'
            ]);
        }
        
        // Borrowing information
        if (preg_match('/(borrow|emprunt|prêt|loan|emprunter)/i', $message)) {
            return $this->getRandomResponse([
                'Pour emprunter un livre, il suffit de cliquer sur "Emprunter" sur la page du livre qui vous intéresse.',
                'Vous pouvez emprunter jusqu\'à 3 livres simultanément pour une durée de 3 semaines.',
                'Consultez votre page "Mes emprunts" pour voir vos emprunts actuels et leur date de retour.'
            ]);
        }
        
        // Return procedures
        if (preg_match('/(return|retour|rendre|rendu)/i', $message)) {
            return $this->getRandomResponse([
                'Pour retourner un livre, rendez-vous sur "Mes emprunts" et cliquez sur "Retourner".',
                'Assurez-vous de retourner vos livres avant la date d\'échéance pour éviter les pénalités.',
                'Si vous avez besoin de plus de temps, vous pouvez prolonger votre emprunt une fois pour 2 semaines supplémentaires.'
            ]);
        }
        
        // Account management - improved pattern to catch more account-related queries
        if (preg_match('/(account|compte|profil|profile|modifier|changer|password|mot de passe|information|settings|paramètre|user|utilisateur)/i', $message)) {
            return $this->getRandomResponse([
                'Pour modifier votre profil, cliquez sur votre nom en haut à droite, puis sélectionnez "Profil".',
                'Vous pouvez changer votre mot de passe et vos informations personnelles dans les paramètres de votre compte.',
                'Pour accéder à vos paramètres, cliquez sur votre nom d\'utilisateur dans le coin supérieur droit, puis sur "Paramètres".'
            ]);
        }
        
        // Book search
        if (preg_match('/(search|find|cherche|trouve|recherche)/i', $message)) {
            return $this->getRandomResponse([
                'Utilisez la barre de recherche en haut de la page pour trouver des livres par titre ou auteur.',
                'Vous pouvez filtrer les résultats par catégorie pour affiner votre recherche.',
                'Si vous cherchez des recommandations, n\'hésitez pas à me demander des suggestions.'
            ]);
        }
        
        // Adding books
        if (preg_match('/(add|ajouter|nouveau|new)/i', $message)) {
            return $this->getRandomResponse([
                'Pour ajouter un livre, cliquez sur "Ajouter un livre" dans le menu principal.',
                'Remplissez le formulaire avec les détails du livre : titre, auteur, description, etc.',
                'N\'oubliez pas d\'ajouter une image de couverture pour rendre votre livre plus attrayant.'
            ]);
        }
        
        // Help requests - moved after more specific patterns to avoid catching specific queries
        if (preg_match('/(help|aide|comment|how)/i', $message)) {
            return $this->getRandomResponse([
                'Je suis là pour vous aider ! Posez-moi des questions sur l\'emprunt, le retour ou la recherche de livres.',
                'Vous pouvez me demander comment emprunter un livre, comment le retourner, ou comment trouver des recommandations.',
                'N\'hésitez pas à me poser des questions spécifiques sur le fonctionnement de BookShare.'
            ]);
        }
        
        // Default responses
        return $this->getRandomResponse([
            'Je ne suis pas sûr de comprendre. Pouvez-vous reformuler votre question ?',
            'Désolé, je n\'ai pas compris. Pouvez-vous être plus précis ?',
            'Je suis là pour vous aider avec BookShare. N\'hésitez pas à me poser des questions sur l\'emprunt, le retour ou la recherche de livres.',
            'Je peux vous aider à trouver des livres, emprunter ou retourner des ouvrages. Comment puis-je vous assister aujourd\'hui ?'
        ]);
    }
    
    /**
     * Get a random response from an array of possible responses
     *
     * @param array $responses Array of possible responses
     * @return string Random response
     */
    private function getRandomResponse(array $responses): string
    {
        return $responses[array_rand($responses)];
    }
}
