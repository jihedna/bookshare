<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\LoanController as AdminLoanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Define admin gate
Gate::define('admin', function ($user) {
    return $user->isAdmin();
});

// Front routes
Route::get('/', [App\Http\Controllers\FrontController::class, 'home'])->name('home');

// Dashboard route (for Laravel Breeze)
Route::get('/dashboard', function () {
    // Si l'utilisateur est admin, rediriger vers le tableau de bord admin
    if (Auth::check() && Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    
    // Sinon, rediriger vers la liste des livres
    return redirect()->route('books.index');
})->middleware(['auth'])->name('dashboard');

// Public book routes
Route::get('/books', [BookController::class, 'index'])->name('books.index');

// Protected book routes
Route::middleware('auth')->group(function () {
    // Mes Livres route
    Route::get('/my-books', [BookController::class, 'myBooks'])->name('books.my');
    
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    
    // Chatbot routes
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
});

// This route must come after the /books/create route to avoid conflicts
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show')->where('id', '[0-9]+');

Route::resource('loans', LoanController::class)->only(['index', 'store', 'update'])->middleware('auth');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::resource('books', AdminBookController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('loans', AdminLoanController::class)->only(['index', 'show', 'update']);
    Route::resource('users', AdminUserController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
