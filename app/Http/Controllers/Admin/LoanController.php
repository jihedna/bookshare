<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Entities\Loan;
use App\Domain\Repositories\LoanRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    protected LoanRepository $loanRepository;

    public function __construct(LoanRepository $loanRepository)
    {
        $this->loanRepository = $loanRepository;
    }

    /**
     * Display a listing of the loans.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', Loan::STATUS_REQUESTED);
        
        if ($status === 'all') {
            $loans = $this->loanRepository->findAll();
        } else {
            $loans = $this->loanRepository->findByStatus($status);
        }
        
        return view('admin.loans.index', [
            'loans' => $loans,
            'currentStatus' => $status,
            'statuses' => [
                'all' => 'Tous',
                Loan::STATUS_REQUESTED => 'Demandés',
                Loan::STATUS_APPROVED => 'Approuvés',
                Loan::STATUS_REJECTED => 'Rejetés',
                Loan::STATUS_RETURNED => 'Retournés'
            ]
        ]);
    }

    /**
     * Display the specified loan.
     */
    public function show(int $id)
    {
        $loan = $this->loanRepository->findById($id);
        
        if (!$loan) {
            abort(404);
        }
        
        return view('admin.loans.show', [
            'loan' => $loan
        ]);
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(Request $request, int $id)
    {
        $loan = $this->loanRepository->findById($id);
        
        if (!$loan) {
            abort(404);
        }
        
        $action = $request->input('action');
        
        if ($action === 'approve' && $loan->isRequested()) {
            $loan->approve();
            $message = 'Emprunt approuvé avec succès.';
        } elseif ($action === 'reject' && $loan->isRequested()) {
            $loan->reject();
            $message = 'Emprunt rejeté avec succès.';
        } elseif ($action === 'return' && $loan->isApproved()) {
            $loan->markAsReturned();
            $message = 'Livre marqué comme retourné avec succès.';
        } else {
            return redirect()->back()->withErrors(['action' => 'Action non valide pour le statut actuel de l\'emprunt.']);
        }
        
        $this->loanRepository->save($loan);
        
        return redirect()->route('admin.loans.index')
            ->with('success', $message);
    }
}
