<?php

namespace App\Http\Controllers;

use App\Domain\Entities\Book;
use App\Domain\Entities\Category;
use App\Domain\Repositories\BookRepository;
use App\Domain\Repositories\CategoryRepository;
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
        // Ajout de la recherche par mot-clé
        $query = $request->get('query');
        if ($query) {
            $books = $this->bookRepository->search($query, 12);
        } else {
            $books = $this->bookRepository->findLatest(12);
        }
        
        return view('front.books.index', [
            'books' => $books,
            'query' => $query
        ]);
    }

    /**
     * Display a listing of the user's books.
     */
    public function myBooks()
    {
        $user = EntityManager::find(\App\Domain\Entities\User::class, auth()->id());
        $books = $this->bookRepository->findByOwner($user);
        
        return view('front.books.my-books', [
            'books' => $books,
            'title' => 'Mes Livres'
        ]);
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $categories = $this->categoryRepository->findAll();
        
        return view('front.books.create', [
            'categories' => $categories
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
        
        // Set the current user as the owner
        $user = EntityManager::find(\App\Domain\Entities\User::class, auth()->id());
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
            try {
                // Validation supplémentaire des types de fichiers
                $request->validate([
                    'cover' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                
                // Stockage simple sans redimensionnement
                $path = $request->file('cover')->store('covers', 'public');
                $book->setCoverPath($path);
            } catch (\Exception $e) {
                // Log l'erreur mais continuer sans image
                \Log::error('Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
            }
        }
        
        // Use AI service to generate summary and tags
        if ($book->getDescription()) {
            try {
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
            } catch (\Exception $e) {
                // Log l'erreur mais continuer sans résumé AI
                \Log::error('Erreur lors de la génération AI: ' . $e->getMessage());
            }
        }
        
        // Save the book
        $this->bookRepository->save($book);
        
        return redirect()->route('books.show', $book->getId())
            ->with('success', 'Livre ajouté avec succès.');
    }

    /**
     * Display the specified book.
     */
    public function show($id)
    {
        // Convert to integer if it's a numeric string
        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            abort(404);
        }
        
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        // Get similar books based on categories
        $similarBooks = $this->bookRepository->findSimilarBooks($book);
        
        return view('front.books.show', [
            'book' => $book,
            'similarBooks' => $similarBooks
        ]);
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit($id)
    {
        // Convert to integer if it's a numeric string
        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            abort(404);
        }
        
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        $this->authorize('update', $book);
        
        $categories = $this->categoryRepository->findAll();
        
        return view('front.books.edit', [
            'book' => $book,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified book in storage.
     */
    public function update(UpdateBookRequest $request, $id)
    {
        // Convert to integer if it's a numeric string
        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            abort(404);
        }
        
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        $this->authorize('update', $book);
        
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
            try {
                // Validation supplémentaire des types de fichiers
                $request->validate([
                    'cover' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                
                // Delete old cover if exists
                if ($book->getCoverPath()) {
                    Storage::disk('public')->delete($book->getCoverPath());
                }
                
                // Stockage simple sans redimensionnement
                $path = $request->file('cover')->store('covers', 'public');
                $book->setCoverPath($path);
            } catch (\Exception $e) {
                // Log l'erreur mais continuer sans image
                \Log::error('Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
            }
        }
        
        // Use AI service to update summary and tags if description changed
        if (isset($validated['description']) && $book->getDescription()) {
            try {
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
            } catch (\Exception $e) {
                // Log l'erreur mais continuer sans résumé AI
                \Log::error('Erreur lors de la génération AI: ' . $e->getMessage());
            }
        }
        
        // Save the book
        $this->bookRepository->save($book);
        
        return redirect()->route('books.show', $book->getId())
            ->with('success', 'Livre mis à jour avec succès.');
    }

    /**
     * Remove the specified book from storage.
     */
    public function destroy($id)
    {
        // Convert to integer if it's a numeric string
        if (is_numeric($id)) {
            $id = (int) $id;
        } else {
            abort(404);
        }
        
        $book = $this->bookRepository->findById($id);
        
        if (!$book) {
            abort(404);
        }
        
        $this->authorize('delete', $book);
        
        // Delete cover image if exists
        if ($book->getCoverPath()) {
            Storage::disk('public')->delete($book->getCoverPath());
        }
        
        $this->bookRepository->delete($book);
        
        return redirect()->route('books.index')
            ->with('success', 'Livre supprimé avec succès.');
    }
}
