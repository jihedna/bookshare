<?php

namespace Tests\Feature;

use App\Domain\Entities\Book;
use App\Domain\Entities\Loan;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\TestCase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $borrower;
    protected $adminUser;
    protected $book;

    public function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->owner = new User();
        $this->owner->setName('Book Owner');
        $this->owner->setEmail('owner@test.com');
        $this->owner->setPassword(Hash::make('password'));
        $this->owner->setRoles(['ROLE_USER']);
        EntityManager::persist($this->owner);

        $this->borrower = new User();
        $this->borrower->setName('Book Borrower');
        $this->borrower->setEmail('borrower@test.com');
        $this->borrower->setPassword(Hash::make('password'));
        $this->borrower->setRoles(['ROLE_USER']);
        EntityManager::persist($this->borrower);

        $this->adminUser = new User();
        $this->adminUser->setName('Admin User');
        $this->adminUser->setEmail('admin@test.com');
        $this->adminUser->setPassword(Hash::make('password'));
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        EntityManager::persist($this->adminUser);

        // Create a test book
        $this->book = new Book();
        $this->book->setTitle('Test Book for Loan');
        $this->book->setAuthor('Test Author');
        $this->book->setOwner($this->owner);
        EntityManager::persist($this->book);

        EntityManager::flush();
    }

    /** @test */
    public function unauthenticated_users_cannot_request_loans()
    {
        $response = $this->post(route('loans.store'), [
            'book_id' => $this->book->getId(),
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_request_loans()
    {
        $this->actingAs($this->borrower);

        $response = $this->post(route('loans.store'), [
            'book_id' => $this->book->getId(),
        ]);

        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        // Check if the loan was created
        $loan = EntityManager::getRepository(Loan::class)->findOneBy([
            'book' => $this->book,
            'borrower' => $this->borrower,
        ]);

        $this->assertNotNull($loan);
        $this->assertEquals(Loan::STATUS_REQUESTED, $loan->getStatus());
    }

    /** @test */
    public function users_cannot_borrow_their_own_books()
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('loans.store'), [
            'book_id' => $this->book->getId(),
        ]);

        $response->assertSessionHasErrors('book_id');

        // Check that no loan was created
        $loan = EntityManager::getRepository(Loan::class)->findOneBy([
            'book' => $this->book,
            'borrower' => $this->owner,
        ]);

        $this->assertNull($loan);
    }

    /** @test */
    public function owners_can_approve_loan_requests()
    {
        // Create a loan request
        $loan = new Loan();
        $loan->setBook($this->book);
        $loan->setBorrower($this->borrower);
        $loan->setStatus(Loan::STATUS_REQUESTED);
        EntityManager::persist($loan);
        EntityManager::flush();

        // Act as the book owner
        $this->actingAs($this->owner);

        // Approve the loan
        $response = $this->put(route('admin.loans.update', $loan->getId()), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check if the loan was approved
        $this->assertEquals(Loan::STATUS_APPROVED, $loan->getStatus());
    }

    /** @test */
    public function owners_can_reject_loan_requests()
    {
        // Create a loan request
        $loan = new Loan();
        $loan->setBook($this->book);
        $loan->setBorrower($this->borrower);
        $loan->setStatus(Loan::STATUS_REQUESTED);
        EntityManager::persist($loan);
        EntityManager::flush();

        // Act as the book owner
        $this->actingAs($this->owner);

        // Reject the loan
        $response = $this->put(route('admin.loans.update', $loan->getId()), [
            'action' => 'reject',
        ]);

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check if the loan was rejected
        $this->assertEquals(Loan::STATUS_REJECTED, $loan->getStatus());
    }

    /** @test */
    public function borrowers_can_mark_loans_as_returned()
    {
        // Create an approved loan
        $loan = new Loan();
        $loan->setBook($this->book);
        $loan->setBorrower($this->borrower);
        $loan->setStatus(Loan::STATUS_APPROVED);
        EntityManager::persist($loan);
        EntityManager::flush();

        // Act as the borrower
        $this->actingAs($this->borrower);

        // Mark the loan as returned
        $response = $this->put(route('loans.update', $loan->getId()));

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check if the loan was marked as returned
        $this->assertEquals(Loan::STATUS_RETURNED, $loan->getStatus());
        $this->assertNotNull($loan->getEndAt());
    }

    /** @test */
    public function non_borrowers_cannot_mark_loans_as_returned()
    {
        // Create an approved loan
        $loan = new Loan();
        $loan->setBook($this->book);
        $loan->setBorrower($this->borrower);
        $loan->setStatus(Loan::STATUS_APPROVED);
        EntityManager::persist($loan);
        EntityManager::flush();

        // Create another user
        $anotherUser = new User();
        $anotherUser->setName('Another User');
        $anotherUser->setEmail('another@test.com');
        $anotherUser->setPassword(Hash::make('password'));
        $anotherUser->setRoles(['ROLE_USER']);
        EntityManager::persist($anotherUser);
        EntityManager::flush();

        // Act as another user
        $this->actingAs($anotherUser);

        // Try to mark the loan as returned
        $response = $this->put(route('loans.update', $loan->getId()));

        $response->assertStatus(403);

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check that the loan status didn't change
        $this->assertEquals(Loan::STATUS_APPROVED, $loan->getStatus());
        $this->assertNull($loan->getEndAt());
    }

    /** @test */
    public function admin_can_manage_any_loan()
    {
        // Create a loan request
        $loan = new Loan();
        $loan->setBook($this->book);
        $loan->setBorrower($this->borrower);
        $loan->setStatus(Loan::STATUS_REQUESTED);
        EntityManager::persist($loan);
        EntityManager::flush();

        // Act as admin
        $this->actingAs($this->adminUser);

        // View the loan details
        $response = $this->get(route('admin.loans.show', $loan->getId()));
        $response->assertStatus(200);

        // Approve the loan
        $response = $this->put(route('admin.loans.update', $loan->getId()), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check if the loan was approved
        $this->assertEquals(Loan::STATUS_APPROVED, $loan->getStatus());

        // Mark the loan as returned
        $response = $this->put(route('admin.loans.update', $loan->getId()), [
            'action' => 'return',
        ]);

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($loan);

        // Check if the loan was marked as returned
        $this->assertEquals(Loan::STATUS_RETURNED, $loan->getStatus());
        $this->assertNotNull($loan->getEndAt());
    }
}
