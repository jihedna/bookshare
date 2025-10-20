<?php

namespace App\Policies;

use App\Domain\Entities\Book;
use App\Domain\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any books.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the book.
     */
    public function view(User $user, Book $book): bool
    {
        return true; // Everyone can view books
    }

    /**
     * Determine whether the user can create books.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create books
    }

    /**
     * Determine whether the user can update the book.
     */
    public function update(User $user, Book $book): bool
    {
        // User can update if they are the owner or an admin
        return $user->getId() === $book->getOwner()->getId() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the book.
     */
    public function delete(User $user, Book $book): bool
    {
        // User can delete if they are the owner or an admin
        return $user->getId() === $book->getOwner()->getId() || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the book.
     */
    public function restore(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the book.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return $user->isAdmin();
    }
}
