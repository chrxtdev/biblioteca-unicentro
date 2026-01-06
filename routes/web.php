<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::get('/livros', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/enviar-livro', [BookController::class, 'create'])->name('books.create');
    Route::post('/livros', [BookController::class, 'store'])->name('books.store');
});

use App\Models\Book; // Não esqueça de importar

Route::get('/dashboard', function () {
    $books = Book::where('is_verified', true)->get();

    return view('dashboard', ['books' => $books]);
})->middleware(['auth', 'verified'])->name('dashboard');
