<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Entities\Book;
use App\Domain\Repositories\BookRepository;
use App\Domain\Repositories\CategoryRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\AiNlpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use LaravelDoctrine\ORM\Facades\EntityManager;

class BookController extends Controller
{
    protected BookRepository $bookRepository;
    protected CategoryRepository $categoryRepository;
    protected AiNlpService $aiService;

    public function __construct(
        BookRepository $bookRepository,
        CategoryRepository $categoryRepository,
        AiNlpService $aiService
    ) {
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
        $this->aiService = $aiService;
    }

    /**
     * Display a listing of the books.
     */
    public function index(Request $request)
    {
        $query = $request->input('query');
        
        if ($query) {
            $books = $this->bookRepository->searchBooks($query);
        } else {
            $books = $this->bookRepository->findAll();
        }
        
        return view('admin.books.index', [
            'books' => $books,
            'query' => $query
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = $this->categoryRepository->findAll();
        $users = EntityManager::getRepository(\App\Domain\Entities\User::class)->findAll();
        
        return view('admin.books.create', [
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Store a newly created book in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();
        
        $book = new Book();
        $book->setTitle($validated['title']);
        $book->setAuthor($validated['author']);
        $book->setDescription($validated['description'] ?? null);
        
        // Set the owner (admin can choose any user)
        $userId = $request->input('owner_id');
        $user = EntityManager::find(\App\Domain\Entities\User::class, $userId);
        
        if (!$user) {
            return redirect()->back()->withErrors(['owner_id' => 'L\'utilisateur sélectionné n\'existe pas.']);
        }
        
        $book->setOwner($user);
        
        // Process categories
        if (isset($validated['categories'])) {
            foreach ($validated['categories'] as $categoryId) {
                $category = $this->categoryRepository->findById($categoryId);
                if ($category) {
                    $book->addCategory($category);
                }
            }
        }
        
        // Process cover image if uploaded
        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            $path = $request->file('cover')->store('covers', 'public');
            $book->setCoverPath($path);
        }
        
        // Use AI service to generate summary and tags
        if ($book->getDescription()) {
            $aiResult = $this->aiService->generateSummaryAndTags(
                $book->getTitle(),
                $book->getDescription()
            );
            
            if (!empty($aiResult['summary'])) {
                $book->setSummary($aiResult['summary']);
            }
            
            // Process AI-generated tags
            if (!empty($aiResult['tags'])) {
                foreach ($aiResult['tags'] as $tagName) {
                    // Find existing category or skip
                    $category = $this->categoryRepository->findByName($tagName);
                    if ($category) {
                        $book->addCategory($category);
                    }
                }
            }
        }
        
        // Save the book
        $this->bookRepository->save($book);
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Livre ajouté avec succès.');
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(int $id)
    {
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        $categories = $this->categoryRepository->findAll();
        $users = EntityManager::getRepository(\App\Domain\Entities\User::class)->findAll();
        
        return view('admin.books.edit', [
            'book' => $book,
            'categories' => $categories,
            'users' => $users
        ]);
    }

    /**
     * Update the specified book in storage.
     */
    public function update(UpdateBookRequest $request, int $id)
    {
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        $validated = $request->validated();
        
        if (isset($validated['title'])) {
            $book->setTitle($validated['title']);
        }
        
        if (isset($validated['author'])) {
            $book->setAuthor($validated['author']);
        }
        
        if (array_key_exists('description', $validated)) {
            $book->setDescription($validated['description']);
        }
        
        // Update owner if provided
        if ($request->has('owner_id')) {
            $userId = $request->input('owner_id');
            $user = EntityManager::find(\App\Domain\Entities\User::class, $userId);
            
            if (!$user) {
                return redirect()->back()->withErrors(['owner_id' => 'L\'utilisateur sélectionné n\'existe pas.']);
            }
            
            $book->setOwner($user);
        }
        
        // Process categories if provided
        if (isset($validated['categories'])) {
            // Remove all current categories
            foreach ($book->getCategories() as $category) {
                $book->removeCategory($category);
            }
            
            // Add new categories
            foreach ($validated['categories'] as $categoryId) {
                $category = $this->categoryRepository->findById($categoryId);
                if ($category) {
                    $book->addCategory($category);
                }
            }
        }
        
        // Process cover image if uploaded
        if ($request->hasFile('cover') && $request->file('cover')->isValid()) {
            // Delete old cover if exists
            if ($book->getCoverPath()) {
                Storage::disk('public')->delete($book->getCoverPath());
            }
            
            $path = $request->file('cover')->store('covers', 'public');
            $book->setCoverPath($path);
        }
        
        // Use AI service to update summary and tags if description changed
        if (isset($validated['description']) && $book->getDescription()) {
            $aiResult = $this->aiService->generateSummaryAndTags(
                $book->getTitle(),
                $book->getDescription()
            );
            
            if (!empty($aiResult['summary'])) {
                $book->setSummary($aiResult['summary']);
            }
        }
        
        // Save the book
        $this->bookRepository->save($book);
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Livre mis à jour avec succès.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy(int $id)
    {
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        // Delete cover image if exists
        if ($book->getCoverPath()) {
            Storage::disk('public')->delete($book->getCoverPath());
        }
        
        $this->bookRepository->delete($book);
        
        return redirect()->route('admin.books.index')
            ->with('success', 'Livre supprimé avec succès.');
    }
}
