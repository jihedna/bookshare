<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Book;
use App\Domain\Entities\User;
use App\Domain\Entities\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

class BookRepository
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Find a book by its ID
     *
     * @param int $id
     * @return Book|null
     */
    public function findById(int $id): ?Book
    {
        return $this->entityManager->find(Book::class, $id);
    }

    /**
     * Find all books
     *
     * @return array|Collection
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }

    /**
     * Find latest books
     *
     * @param int $limit
     * @return array|Collection
     */
    public function findLatest(int $limit = 10)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit);
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Search for books by title, author or category
     *
     * @param string $query The search query
     * @param int $limit Maximum number of results to return
     * @return array|Collection Books matching the search criteria
     */
    public function search(string $query, int $limit = 12)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->leftJoin('b.categories', 'c')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('LOWER(b.title)', ':query'),
                $queryBuilder->expr()->like('LOWER(b.author)', ':query'),
                $queryBuilder->expr()->like('LOWER(b.description)', ':query'),
                $queryBuilder->expr()->like('LOWER(c.name)', ':query')
            ))
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit);
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find books by owner
     *
     * @param User $owner
     * @return array|Collection
     */
    public function findByOwner(User $owner)
    {
        return $this->entityManager->getRepository(Book::class)->findBy(['owner' => $owner]);
    }

    /**
     * Find books by category
     *
     * @param Category $category
     * @return array|Collection
     */
    public function findByCategory(Category $category)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin('b.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->orderBy('b.createdAt', 'DESC');
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find similar books based on categories
     *
     * @param Book $book
     * @param int $limit
     * @return array|Collection
     */
    public function findSimilarBooks(Book $book, int $limit = 4)
    {
        $categories = $book->getCategories();
        
        if ($categories->isEmpty()) {
            // If no categories, return latest books excluding the current one
            $queryBuilder = $this->entityManager->createQueryBuilder();
            
            $queryBuilder->select('b')
                ->from(Book::class, 'b')
                ->where('b.id != :bookId')
                ->setParameter('bookId', $book->getId())
                ->orderBy('b.createdAt', 'DESC')
                ->setMaxResults($limit);
            
            return $queryBuilder->getQuery()->getResult();
        }
        
        // Get category IDs
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        
        $queryBuilder = $this->entityManager->createQueryBuilder();
        
        $queryBuilder->select('b')
            ->from(Book::class, 'b')
            ->innerJoin('b.categories', 'c')
            ->where('c.id IN (:categoryIds)')
            ->andWhere('b.id != :bookId')
            ->setParameter('categoryIds', $categoryIds)
            ->setParameter('bookId', $book->getId())
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit);
        
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Save a book
     *
     * @param Book $book
     * @return void
     */
    public function save(Book $book): void
    {
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }

    /**
     * Delete a book
     *
     * @param Book $book
     * @return void
     */
    public function delete(Book $book): void
    {
        $this->entityManager->remove($book);
        $this->entityManager->flush();
    }
}