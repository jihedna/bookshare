<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = $this->categoryRepository->findAll();
        
        return view('admin.categories.index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
            ]
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'name.unique' => 'Cette catégorie existe déjà.'
        ]);
        
        $category = new Category();
        $category->setName($validated['name']);
        
        $this->categoryRepository->save($category);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(int $id)
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }
        
        return view('admin.categories.edit', [
            'category' => $category
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, int $id)
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($id)
            ]
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.max' => 'Le nom de la catégorie ne peut pas dépasser 255 caractères.',
            'name.unique' => 'Cette catégorie existe déjà.'
        ]);
        
        $category->setName($validated['name']);
        
        $this->categoryRepository->save($category);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(int $id)
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }
        
        // Check if the category has books
        if (!$category->getBooks()->isEmpty()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle est associée à des livres.');
        }
        
        $this->categoryRepository->delete($category);
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
