<?php

namespace Database\Seeders;

use App\Domain\Entities\Book;
use App\Domain\Entities\Category;
use App\Domain\Entities\Loan;
use App\Domain\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ORM\Facades\EntityManager;

class SimpleDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@bookshare.local');
        $admin->setPassword(Hash::make('password'));
        $admin->setRoles(['ROLE_ADMIN']);
        EntityManager::persist($admin);
        
        // Create a regular user
        $user = new User();
        $user->setName('User');
        $user->setEmail('user@bookshare.local');
        $user->setPassword(Hash::make('password'));
        $user->setRoles(['ROLE_USER']);
        EntityManager::persist($user);
        
        // Create categories
        $categories = [];
        $categoryNames = ['Roman', 'SF', 'Tech'];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            EntityManager::persist($category);
            $categories[] = $category;
        }
        
        // Create books
        // Book 1
        $book1 = new Book();
        $book1->setTitle('Introduction à Laravel');
        $book1->setAuthor('John Doe');
        $book1->setDescription('Un livre pour apprendre Laravel, le framework PHP moderne.');
        $book1->setSummary('Guide complet pour débutants sur Laravel.');
        $book1->setOwner($admin);
        $book1->addCategory($categories[2]); // Tech category
        EntityManager::persist($book1);
        
        // Book 2
        $book2 = new Book();
        $book2->setTitle('Dune');
        $book2->setAuthor('Frank Herbert');
        $book2->setDescription('Le chef-d\'œuvre de la science-fiction qui a inspiré des générations.');
        $book2->setSummary('Une épopée sur une planète désertique où l\'eau est plus précieuse que l\'or.');
        $book2->setOwner($user);
        $book2->addCategory($categories[1]); // SF category
        EntityManager::persist($book2);
        
        // Book 3
        $book3 = new Book();
        $book3->setTitle('Les Misérables');
        $book3->setAuthor('Victor Hugo');
        $book3->setDescription('Un roman historique emblématique de la littérature française.');
        $book3->setSummary('L\'histoire de Jean Valjean et de sa rédemption dans la France du 19e siècle.');
        $book3->setOwner($admin);
        $book3->addCategory($categories[0]); // Roman category
        EntityManager::persist($book3);
        
        // Create a loan
        $loan = new Loan();
        $loan->setBook($book2);
        $loan->setBorrower($admin);
        $loan->setStatus(Loan::STATUS_APPROVED);
        EntityManager::persist($loan);
        
        // Save all entities
        EntityManager::flush();
        
        $this->command->info('Simple demo data seeded successfully!');
        $this->command->info('Admin login: admin@bookshare.local / password');
        $this->command->info('User login: user@bookshare.local / password');
    }
}
