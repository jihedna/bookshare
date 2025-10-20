<?php

namespace Tests\Feature;

use App\Domain\Entities\Book;
use App\Domain\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\TestCase;

class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $owner;
    protected $otherUser;
    protected $adminUser;

    public function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->owner = new User();
        $this->owner->setName('Test Owner');
        $this->owner->setEmail('owner@test.com');
        $this->owner->setPassword(Hash::make('password'));
        $this->owner->setRoles(['ROLE_USER']);
        EntityManager::persist($this->owner);

        $this->otherUser = new User();
        $this->otherUser->setName('Other User');
        $this->otherUser->setEmail('other@test.com');
        $this->otherUser->setPassword(Hash::make('password'));
        $this->otherUser->setRoles(['ROLE_USER']);
        EntityManager::persist($this->otherUser);

        $this->adminUser = new User();
        $this->adminUser->setName('Admin User');
        $this->adminUser->setEmail('admin@test.com');
        $this->adminUser->setPassword(Hash::make('password'));
        $this->adminUser->setRoles(['ROLE_ADMIN']);
        EntityManager::persist($this->adminUser);

        EntityManager::flush();
    }

    /** @test */
    public function unauthenticated_users_cannot_create_books()
    {
        $response = $this->get(route('books.create'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('books.store'), [
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_users_can_create_books()
    {
        Storage::fake('public');

        $this->actingAs($this->owner);

        $response = $this->get(route('books.create'));
        $response->assertStatus(200);

        $response = $this->post(route('books.store'), [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'This is a test book description that is at least 20 characters long.',
            'cover' => UploadedFile::fake()->image('book_cover.jpg'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'author' => 'Test Author',
        ]);

        // Verify the book was created with the correct owner
        $book = EntityManager::getRepository(Book::class)->findOneBy(['title' => 'Test Book']);
        $this->assertEquals($this->owner->getId(), $book->getOwner()->getId());
    }

    /** @test */
    public function validation_errors_are_shown_when_creating_books()
    {
        $this->actingAs($this->owner);

        $response = $this->post(route('books.store'), [
            'title' => '', // Empty title should fail validation
            'author' => 'Test Author',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function owners_can_edit_their_books()
    {
        // Create a book
        $book = new Book();
        $book->setTitle('Original Title');
        $book->setAuthor('Original Author');
        $book->setOwner($this->owner);
        EntityManager::persist($book);
        EntityManager::flush();

        // Act as the owner
        $this->actingAs($this->owner);

        // Access the edit page
        $response = $this->get(route('books.edit', $book->getId()));
        $response->assertStatus(200);

        // Update the book
        $response = $this->put(route('books.update', $book->getId()), [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Refresh the entity
        EntityManager::refresh($book);

        // Check if the book was updated
        $this->assertEquals('Updated Title', $book->getTitle());
        $this->assertEquals('Updated Author', $book->getAuthor());
    }

    /** @test */
    public function non_owners_cannot_edit_books()
    {
        // Create a book
        $book = new Book();
        $book->setTitle('Test Book');
        $book->setAuthor('Test Author');
        $book->setOwner($this->owner);
        EntityManager::persist($book);
        EntityManager::flush();

        // Act as another user
        $this->actingAs($this->otherUser);

        // Try to access the edit page
        $response = $this->get(route('books.edit', $book->getId()));
        $response->assertStatus(403);

        // Try to update the book
        $response = $this->put(route('books.update', $book->getId()), [
            'title' => 'Updated Title',
            'author' => 'Updated Author',
        ]);
        $response->assertStatus(403);

        // Refresh the entity
        EntityManager::refresh($book);

        // Check that the book was not updated
        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('Test Author', $book->getAuthor());
    }

    /** @test */
    public function owners_can_delete_their_books()
    {
        // Create a book
        $book = new Book();
        $book->setTitle('Book to Delete');
        $book->setAuthor('Author');
        $book->setOwner($this->owner);
        EntityManager::persist($book);
        EntityManager::flush();

        $bookId = $book->getId();

        // Act as the owner
        $this->actingAs($this->owner);

        // Delete the book
        $response = $this->delete(route('books.destroy', $bookId));
        $response->assertRedirect(route('books.index'));
        $response->assertSessionHas('success');

        // Check if the book was deleted
        $deletedBook = EntityManager::getRepository(Book::class)->find($bookId);
        $this->assertNull($deletedBook);
    }

    /** @test */
    public function non_owners_cannot_delete_books()
    {
        // Create a book
        $book = new Book();
        $book->setTitle('Book to Not Delete');
        $book->setAuthor('Author');
        $book->setOwner($this->owner);
        EntityManager::persist($book);
        EntityManager::flush();

        $bookId = $book->getId();

        // Act as another user
        $this->actingAs($this->otherUser);

        // Try to delete the book
        $response = $this->delete(route('books.destroy', $bookId));
        $response->assertStatus(403);

        // Check that the book still exists
        $book = EntityManager::getRepository(Book::class)->find($bookId);
        $this->assertNotNull($book);
    }

    /** @test */
    public function admin_can_edit_any_book()
    {
        // Create a book
        $book = new Book();
        $book->setTitle('Book for Admin Edit');
        $book->setAuthor('Author');
        $book->setOwner($this->owner);
        EntityManager::persist($book);
        EntityManager::flush();

        // Act as admin
        $this->actingAs($this->adminUser);

        // Access the edit page
        $response = $this->get(route('admin.books.edit', $book->getId()));
        $response->assertStatus(200);

        // Update the book
        $response = $this->put(route('admin.books.update', $book->getId()), [
            'title' => 'Admin Updated Title',
            'author' => 'Admin Updated Author',
            'owner_id' => $this->otherUser->getId(), // Admin can even change the owner
        ]);

        $response->assertRedirect();

        // Refresh the entity
        EntityManager::refresh($book);

        // Check if the book was updated
        $this->assertEquals('Admin Updated Title', $book->getTitle());
        $this->assertEquals('Admin Updated Author', $book->getAuthor());
        $this->assertEquals($this->otherUser->getId(), $book->getOwner()->getId());
    }
}
