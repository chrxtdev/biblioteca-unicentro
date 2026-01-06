<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status') === 'livro-enviado')
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                     role="alert">
                    <strong class="font-bold">Sucesso!</strong>
                    <span class="block sm:inline">Seu livro foi enviado para análise da bibliotecária.</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Livros Disponíveis na Biblioteca</h3>

                    @if($books->isEmpty())
                        <p>Nenhum livro disponível no momento.</p>
                    @else
                        <ul>
                            @foreach($books as $book)
                                <li class="mb-2 p-2 border-b">
                                    <span class="font-bold">{{ $book->title }}</span>
                                    <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank"
                                       class="text-blue-500 hover:underline ml-2">
                                        Ler PDF
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
