<?php

namespace Database\Seeders;

use App\Domain\Entities\Book;
use App\Domain\Entities\Category;
use App\Domain\Entities\Loan;
use App\Domain\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Faker\Factory as Faker;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');
        
        // Create admin user
        $admin = new User();
        $admin->setName('Admin');
        $admin->setEmail('admin@bookshare.local');
        $admin->setPassword(Hash::make('password'));
        $admin->setRoles(['ROLE_ADMIN']);
        EntityManager::persist($admin);
        
        // Create demo users
        $users = [$admin];
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setName($faker->name());
            $user->setEmail("user{$i}@bookshare.local");
            $user->setPassword(Hash::make('password'));
            $user->setRoles(['ROLE_USER']);
            EntityManager::persist($user);
            $users[] = $user;
        }
        
        // Create categories
        $categoryNames = ['Roman', 'Essai', 'SF', 'Tech', 'Histoire', 'Biographie', 'Policier', 'Poésie'];
        $categories = [];
        
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            EntityManager::persist($category);
            $categories[] = $category;
        }
        
        // Create books
        $books = [];
        for ($i = 1; $i <= 20; $i++) {
            $book = new Book();
            $book->setTitle($faker->sentence(rand(3, 6)));
            $book->setAuthor($faker->name());
            $book->setDescription($faker->paragraphs(rand(2, 5), true));
            $book->setSummary($faker->sentence(rand(10, 20)));
            
            // Randomly assign an owner
            $owner = $users[array_rand($users)];
            $book->setOwner($owner);
            
            // Randomly assign 1-3 categories
            $numCategories = rand(1, 3);
            $randomCategories = $faker->randomElements($categories, $numCategories);
            foreach ($randomCategories as $category) {
                $book->addCategory($category);
            }
            
            EntityManager::persist($book);
            $books[] = $book;
        }
        
        // Create loans with varied statuses
        $loanStatuses = [
            Loan::STATUS_REQUESTED,
            Loan::STATUS_APPROVED,
            Loan::STATUS_REJECTED,
            Loan::STATUS_RETURNED
        ];
        
        for ($i = 0; $i < 10; $i++) {
            $loan = new Loan();
            
            // Get a random book
            $book = $books[array_rand($books)];
            $loan->setBook($book);
            
            // Get a random user that is not the owner
            do {
                $borrower = $users[array_rand($users)];
            } while ($borrower->getId() === $book->getOwner()->getId());
            
            $loan->setBorrower($borrower);
            
            // Set a random status
            $status = $loanStatuses[array_rand($loanStatuses)];
            $loan->setStatus($status);
            
            // Set start date (between 1-30 days ago)
            $startDate = new \DateTime();
            $startDate->modify('-' . rand(1, 30) . ' days');
            $loan->setStartAt($startDate);
            
            // If returned, set end date
            if ($status === Loan::STATUS_RETURNED) {
                $endDate = clone $startDate;
                $endDate->modify('+' . rand(1, 14) . ' days');
                $loan->setEndAt($endDate);
            }
            
            EntityManager::persist($loan);
        }
        
        // Save all entities
        EntityManager::flush();
        
        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Admin login: admin@bookshare.local / password');
        $this->command->info('User logins: user1@bookshare.local through user5@bookshare.local / password');
    }
}
