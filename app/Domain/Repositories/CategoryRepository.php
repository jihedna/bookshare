<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Category;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CategoryRepository
{
    private EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Category::class);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function findByName(string $name): ?Category
    {
        return $this->repository->findOneBy(['name' => $name]);
    }

    public function findOrCreateByName(string $name): Category
    {
        $category = $this->findByName($name);
        
        if (!$category) {
            $category = new Category();
            $category->setName($name);
            $this->save($category);
        }
        
        return $category;
    }

    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function delete(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
