<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Book;
use App\Domain\Entities\Category;
use Doctrine\ORM\EntityManagerInterface;

class FrontController extends Controller
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function home()
    {
        $bookRepository = $this->entityManager->getRepository(Book::class);
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $featuredBooks = $bookRepository->findBy([], ['id' => 'DESC'], 8);
        $categories = $categoryRepository->findAll();

        return view('front.home', [
            'featuredBooks' => $featuredBooks,
            'categories' => $categories
        ]);
    }
}