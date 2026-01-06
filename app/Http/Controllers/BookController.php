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
            'course' => 'required|string',
            'file_path' => 'required|file|mimes:pdf|max:10240',
            'cover_path' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('livros_pdfs', 'public');
        } else {
            return back()->with('error', 'O arquivo é obrigatório');
        }

        $capaPath = null;
        if ($request->hasFile('cover_path')) {
            $capaPath = $request->file('cover_path')->store('livros_capas', 'public');
        }


        $caminhoPdf = $request->file('file_path')->store('livros_pdfs', 'public');

        $caminhoCapa = null;
        if ($request->hasFile('cover_path')) {
            $caminhoCapa = $request->file('cover_path')->store('livros_capas', 'public');
        }

        $book = Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'description' => $request->description,
            'course' => $request->course,

            'file_path' => $path,
            'cover_path' => $capaPath,

            'user_id' => auth()->id(),
            'is_verified' => false,
        ]);

        return redirect()->route('books.index')->with('success', 'Livro enviado!');
    }
}
