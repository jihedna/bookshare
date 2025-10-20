<?php

namespace App\Domain\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Notifications\Notifiable;
use LaravelDoctrine\ORM\Facades\EntityManager;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements Authenticatable, CanResetPassword
{
    use CanResetPasswordTrait;
    use Notifiable;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    protected ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    protected ?string $password = null;

    #[ORM\Column(type: 'json', nullable: true)]
    protected ?array $roles = [];

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?DateTimeImmutable $email_verified_at = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    protected ?string $remember_token = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: 'datetime_immutable')]
    protected ?DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Book::class)]
    protected Collection $books;

    #[ORM\OneToMany(mappedBy: 'borrower', targetEntity: Loan::class)]
    protected Collection $loans;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->loans = new ArrayCollection();
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
        $this->roles = [];
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        // Ensure roles is initialized
        if (!isset($this->roles) || !is_array($this->roles)) {
            $this->roles = [];
        }
        
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->email_verified_at;
    }

    public function setEmailVerifiedAt(?DateTimeImmutable $email_verified_at): self
    {
        $this->email_verified_at = $email_verified_at;
        return $this;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at ?? new DateTimeImmutable();
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at ?? new DateTimeImmutable();
    }

    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setOwner($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getOwner() === $this) {
                $book->setOwner(null);
            }
        }

        return $this;
    }

    public function getLoans(): Collection
    {
        return $this->loans;
    }

    public function addLoan(Loan $loan): self
    {
        if (!$this->loans->contains($loan)) {
            $this->loans[] = $loan;
            $loan->setBorrower($this);
        }

        return $this;
    }

    public function removeLoan(Loan $loan): self
    {
        if ($this->loans->removeElement($loan)) {
            // set the owning side to null (unless already changed)
            if ($loan->getBorrower() === $this) {
                $loan->setBorrower(null);
            }
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getId();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->getPassword();
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
    
    /**
     * Get the column name for the password.
     *
     * @return string
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }
    
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->getEmail();
    }
    
    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail(): string
    {
        return $this->getEmail();
    }
    
    /**
     * Fill the model with an array of attributes.
     * 
     * @param array $attributes
     * @return $this
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            // Use setter methods if they exist
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } else {
                // Otherwise set property directly
                $this->$key = $value;
            }
        }
        
        return $this;
    }
    
    /**
     * Update the model with the given attributes.
     *
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes): bool
    {
        $this->fill($attributes);
        return $this->save();
    }
    
    /**
     * Check if the model or given attribute has been modified.
     *
     * @param string|null $attribute
     * @return bool
     */
    public function isDirty($attribute = null): bool
    {
        // Since we don't track changes in this entity, we'll always return false
        // You might want to implement proper change tracking if needed
        return false;
    }
    
    /**
     * Save the model to the database.
     *
     * @return bool
     */
    public function save(): bool
    {
        try {
            EntityManager::persist($this);
            EntityManager::flush();
            return true;
        } catch (\Exception $e) {
            \Log::error('Error saving user: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete the model from the database.
     *
     * @return bool
     */
    public function delete(): bool
    {
        try {
            // First, handle related entities (books and loans)
            foreach ($this->books as $book) {
                $book->setOwner(null);
                EntityManager::persist($book);
            }
            
            // For loans, we need to delete them since they can't have a null borrower
            $loans = $this->loans->toArray(); // Create a copy to avoid modification during iteration
            foreach ($loans as $loan) {
                EntityManager::remove($loan);
            }
            
            // Then remove the user
            EntityManager::remove($this);
            EntityManager::flush();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Dynamically access the user's attributes.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        // Handle common properties that might not be initialized
        if ($key === 'name') {
            return $this->getName();
        }
        
        if ($key === 'email') {
            return $this->getEmail();
        }
        
        if ($key === 'password') {
            return $this->getPassword();
        }
        
        if ($key === 'roles') {
            return $this->getRoles();
        }
        
        if ($key === 'created_at') {
            return $this->getCreatedAt();
        }
        
        if ($key === 'updated_at') {
            return $this->getUpdatedAt();
        }
        
        // For other properties, check if they exist
        if (property_exists($this, $key)) {
            return $this->$key ?? null;
        }
        
        return null;
    }
    
    /**
     * Dynamically set an attribute on the user.
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
