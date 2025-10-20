<?php

namespace App\Policies;

use App\Domain\Entities\Loan;
use App\Domain\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any loans.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their loans
    }

    /**
     * Determine whether the user can view the loan.
     */
    public function view(User $user, Loan $loan): bool
    {
        // User can view if they are the borrower, the book owner, or an admin
        return $user->getId() === $loan->getBorrower()->getId() || 
               $user->getId() === $loan->getBook()->getOwner()->getId() || 
               $user->isAdmin();
    }

    /**
     * Determine whether the user can create loans.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can request loans
    }

    /**
     * Determine whether the user can update the loan.
     */
    public function update(User $user, Loan $loan): bool
    {
        // If the loan is approved and the user is the borrower, they can mark it as returned
        if ($loan->isApproved() && $user->getId() === $loan->getBorrower()->getId()) {
            return true;
        }
        
        // Book owners and admins can approve/reject loan requests
        return $user->getId() === $loan->getBook()->getOwner()->getId() || $user->isAdmin();
    }

    /**
     * Determine whether the user can approve the loan.
     */
    public function approve(User $user, Loan $loan): bool
    {
        // Only book owners and admins can approve loan requests
        return $user->getId() === $loan->getBook()->getOwner()->getId() || $user->isAdmin();
    }

    /**
     * Determine whether the user can reject the loan.
     */
    public function reject(User $user, Loan $loan): bool
    {
        // Only book owners and admins can reject loan requests
        return $user->getId() === $loan->getBook()->getOwner()->getId() || $user->isAdmin();
    }

    /**
     * Determine whether the user can mark the loan as returned.
     */
    public function markAsReturned(User $user, Loan $loan): bool
    {
        // Only borrowers can mark loans as returned
        return $user->getId() === $loan->getBorrower()->getId();
    }

    /**
     * Determine whether the user can delete the loan.
     */
    public function delete(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the loan.
     */
    public function restore(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the loan.
     */
    public function forceDelete(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }
}
