<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Book;
use App\Domain\Entities\Loan;
use App\Domain\Repositories\BookRepository;
use App\Domain\Repositories\LoanRepository;
use App\Http\Requests\StoreLoanRequest;
use Illuminate\Http\Request;
use LaravelDoctrine\ORM\Facades\EntityManager;

class LoanController extends Controller
{
    protected LoanRepository $loanRepository;
    protected BookRepository $bookRepository;

    public function __construct(
        LoanRepository $loanRepository,
        BookRepository $bookRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * Display a listing of the user's loans.
     */
/**
 * Display a listing of the user's loans.
 */
public function index(Request $request)
{
    $user = EntityManager::find(\App\Domain\Entities\User::class, auth()->id());
    
    // Récupérer tous les emprunts de l'utilisateur
    $allLoans = $this->loanRepository->findByBorrower($user);
    
    // Filtrer les emprunts par statut et vérifier que les livres existent
    $borrowedLoans = [];
    $requestedLoans = [];
    $historyLoans = [];
    
    foreach ($allLoans as $loan) {
        try {
            // Vérifier que le livre existe en accédant à une propriété
            $book = $loan->getBook();
            $bookTitle = $book->getTitle(); // Si le livre n'existe pas, cela lancera une exception
            
            if ($loan->getStatus() === 'APPROVED' && $loan->getEndAt() === null) {
                $borrowedLoans[] = $loan;
            } elseif ($loan->getStatus() === 'REQUESTED') {
                $requestedLoans[] = $loan;
            } else {
                $historyLoans[] = $loan;
            }
        } catch (\Exception $e) {
            // Ignorer cet emprunt si le livre n'existe plus
            continue;
        }
    }
    
    // Récupérer les livres que l'utilisateur a prêtés (où il est le propriétaire)
    $allLentLoans = $this->loanRepository->findByBookOwner($user);
    $lentLoans = [];
    
    foreach ($allLentLoans as $loan) {
        try {
            // Vérifier que le livre et l'emprunteur existent
            $book = $loan->getBook();
            $borrower = $loan->getBorrower();
            $bookTitle = $book->getTitle();
            $borrowerName = $borrower->getName();
            
            $lentLoans[] = $loan;
        } catch (\Exception $e) {
            // Ignorer cet emprunt si le livre ou l'emprunteur n'existe plus
            continue;
        }
    }
    
    // Filtrer les emprunts en attente de validation par le propriétaire
    $pendingApprovalLoans = array_filter($lentLoans, function($loan) {
        return $loan->getStatus() === 'REQUESTED';
    });
    
    return view('front.loans.index', [
        'borrowedLoans' => $borrowedLoans,
        'requestedLoans' => $requestedLoans,
        'historyLoans' => $historyLoans,
        'lentLoans' => $lentLoans,
        'pendingApprovalLoans' => $pendingApprovalLoans
    ]);
}
    /**
     * Store a newly created loan request in storage.
     */
    public function store(StoreLoanRequest $request)
    {
        $validated = $request->validated();
        
        $book = $this->bookRepository->findById($validated['book_id']);
        
        if (!$book) {
            return redirect()->back()->withErrors(['book_id' => 'Le livre sélectionné n\'existe pas.']);
        }
        
        // Check if the book is available for loan
        if (!$book->isAvailableForLoan()) {
            return redirect()->back()->withErrors(['book_id' => 'Ce livre n\'est pas disponible pour l\'emprunt actuellement.']);
        }
        
        $user = EntityManager::find(\App\Domain\Entities\User::class, auth()->id());
        
        // Create a new loan request
        $loan = new Loan();
        $loan->setBook($book);
        $loan->setBorrower($user);
        // Status is set to REQUESTED by default in the constructor
        
        $this->loanRepository->save($loan);
        
        return redirect()->route('loans.index')
            ->with('success', 'Demande d\'emprunt envoyée avec succès. Attendez la confirmation du propriétaire.');
    }

    /**
     * Update the specified loan in storage.
     * Users can only mark their loans as returned.
     */
    public function update(Request $request, int $id)
    {
        $loan = $this->loanRepository->findById($id);
        
        if (!$loan) {
            abort(404);
        }
        
        $user = EntityManager::find(\App\Domain\Entities\User::class, auth()->id());
        
        // Vérifier si l'utilisateur est le propriétaire du livre (pour approuver/refuser)
        if ($request->has('status') && in_array($request->status, ['APPROVED', 'REJECTED'])) {
            if ($loan->getBook()->getOwner()->getId() !== $user->getId()) {
                abort(403, 'Vous n\'êtes pas autorisé à approuver ou refuser cet emprunt.');
            }
            
            if ($request->status === 'APPROVED') {
                $loan->approve();
                $message = 'Demande d\'emprunt approuvée avec succès.';
            } else {
                $loan->reject();
                $message = 'Demande d\'emprunt refusée.';
            }
            
            $this->loanRepository->save($loan);
            return redirect()->route('loans.index')->with('success', $message);
        }
        
        // Vérifier si l'utilisateur est l'emprunteur (pour marquer comme retourné)
        if ($request->has('status') && $request->status === 'RETURNED') {
            if ($loan->getBorrower()->getId() !== $user->getId()) {
                abort(403, 'Vous n\'êtes pas autorisé à modifier cet emprunt.');
            }
            
            // Verify that the loan is in APPROVED status
            if (!$loan->isApproved()) {
                return redirect()->back()->withErrors(['status' => 'Seuls les emprunts approuvés peuvent être marqués comme retournés.']);
            }
            
            // Mark the loan as returned
            $loan->markAsReturned();
            $this->loanRepository->save($loan);
            
            return redirect()->route('loans.index')
                ->with('success', 'Livre marqué comme retourné avec succès.');
        }
        
        return redirect()->back()->withErrors(['status' => 'Action non valide.']);
    }
}
