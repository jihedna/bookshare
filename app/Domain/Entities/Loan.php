<?php

namespace App\Domain\Entities;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'loans')]
class Loan
{
    public const STATUS_REQUESTED = 'REQUESTED';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_RETURNED = 'RETURNED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'loans')]
    #[ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id', nullable: false)]
    protected Book $book;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'loans')]
    #[ORM\JoinColumn(name: 'borrower_id', referencedColumnName: 'id', nullable: false)]
    protected User $borrower;

    #[ORM\Column(type: 'datetime')]
    protected DateTimeInterface $startAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTimeInterface $endAt = null;

    #[ORM\Column(type: 'string', length: 20)]
    protected string $status;

    public function __construct()
    {
        $this->startAt = new DateTime();
        $this->status = self::STATUS_REQUESTED;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getBorrower(): User
    {
        return $this->borrower;
    }

    public function setBorrower(User $borrower): self
    {
        $this->borrower = $borrower;
        return $this;
    }

    public function getStartAt(): DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, [self::STATUS_REQUESTED, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_RETURNED])) {
            throw new \InvalidArgumentException("Invalid status");
        }
        
        $this->status = $status;
        return $this;
    }

    public function isRequested(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }

    public function approve(): self
    {
        $this->status = self::STATUS_APPROVED;
        return $this;
    }

    public function reject(): self
    {
        $this->status = self::STATUS_REJECTED;
        return $this;
    }

    public function markAsReturned(): self
    {
        $this->status = self::STATUS_RETURNED;
        $this->endAt = new DateTime();
        return $this;
    }
    
    /**
     * Dynamically access the loan's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        
        return null;
    }
    
    /**
     * Dynamically set an attribute on the loan.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }
}
