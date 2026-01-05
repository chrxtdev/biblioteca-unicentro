<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::where('is_verified', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return $books;
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $dadosValidados = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|file|mimes:pdf|max:10240',
            'cover_path' => 'nullable|image|max:5120',
        ]);

        $caminhoPdf = $request->file('file_path')->store('livros_pdfs', 'public');

        $caminhoCapa = null;
        if ($request->hasFile('cover_path')) {
            $caminhoCapa = $request->file('cover_path')->store('livros_capas', 'public');
        }

        Book::create([
            'title' => $dadosValidados['title'],
            'author' => $dadosValidados['author'],
            'description' => $dadosValidados['description'],
            'file_path' => $caminhoPdf,
            'cover_path' => $caminhoCapa,

            // SEGREDOS DO BACKEND:
            'user_id' => Auth::id(),
            'is_verified' => false,
        ]);

        return redirect()->route('books.index')->with('success', 'Livro enviado para análise!');
    }
}
