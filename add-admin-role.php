<?php

// Script pour ajouter le rôle d'administrateur à un utilisateur

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domain\Entities\User;
use LaravelDoctrine\ORM\Facades\EntityManager;

// Adresse e-mail de l'utilisateur à promouvoir en administrateur
$email = 'admin@bookshare.local'; // Remplacez par votre adresse e-mail

// Trouver l'utilisateur
$user = EntityManager::getRepository(User::class)->findOneBy(['email' => $email]);

if (!$user) {
    echo "Utilisateur avec l'email {$email} non trouvé.\n";
    exit(1);
}

// Vérifier si l'utilisateur est déjà administrateur
if ($user->isAdmin()) {
    echo "L'utilisateur {$email} est déjà administrateur.\n";
    exit(0);
}

// Ajouter le rôle ROLE_ADMIN
$roles = $user->getRoles();
$roles[] = 'ROLE_ADMIN';
$user->setRoles(array_unique($roles));

// Sauvegarder les modifications
EntityManager::persist($user);
EntityManager::flush();

echo "L'utilisateur {$email} a été promu administrateur avec succès.\n";
