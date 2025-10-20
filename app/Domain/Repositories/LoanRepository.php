<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Book;
use App\Domain\Entities\Loan;
use App\Domain\Entities\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class LoanRepository
{
    private EntityRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Loan::class);
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?Loan
    {
        return $this->repository->find($id);
    }

    public function findByBorrower(User $borrower)
    {
        return $this->repository->findBy(['borrower' => $borrower]);
    }

    public function findByBook(Book $book)
    {
        return $this->repository->findBy(['book' => $book]);
    }

    /**
     * Find loans where the given user is the book owner
     */
    public function findByBookOwner(User $owner)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Loan::class, 'l')
            ->join('l.book', 'b')
            ->where('b.owner = :owner')
            ->setParameter('owner', $owner)
            ->orderBy('l.startAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByStatus(string $status)
    {
        return $this->repository->findBy(['status' => $status]);
    }

    public function findByCriteria(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findActiveLoansForBook(Book $book): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Loan::class, 'l')
            ->where('l.book = :book')
            ->andWhere('l.status = :status')
            ->andWhere('l.endAt IS NULL')
            ->setParameter('book', $book)
            ->setParameter('status', Loan::STATUS_APPROVED);

        return $qb->getQuery()->getResult();
    }

    public function findActiveLoansForUser(User $user): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Loan::class, 'l')
            ->where('l.borrower = :user')
            ->andWhere('l.status = :status')
            ->andWhere('l.endAt IS NULL')
            ->setParameter('user', $user)
            ->setParameter('status', Loan::STATUS_APPROVED);

        return $qb->getQuery()->getResult();
    }

    public function save(Loan $loan): void
    {
        $this->entityManager->persist($loan);
        $this->entityManager->flush();
    }

    public function delete(Loan $loan): void
    {
        $this->entityManager->remove($loan);
        $this->entityManager->flush();
    }
}
