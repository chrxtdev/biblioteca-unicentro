<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/livros', [BookController::class, 'index'])->name('books.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/enviar-livro', [BookController::class, 'create'])->name('books.create');
    Route::post('/livros', [BookController::class, 'store'])->name('books.store');
});
