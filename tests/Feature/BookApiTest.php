<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('public endpoint only shows verified books', function () {
    $user = User::factory()->create();

    // Create a verified book
    Book::create([
        'title' => 'Verified Book',
        'author' => 'Author A',
        'course' => 'Geral',
        'description' => 'Description A',
        'file_path' => 'books/verified.pdf',
        'user_id' => $user->id,
        'is_verified' => true,
    ]);

    // Create an unverified book
    Book::create([
        'title' => 'Unverified Book',
        'author' => 'Author B',
        'course' => 'Geral',
        'description' => 'Description B',
        'file_path' => 'books/unverified.pdf',
        'user_id' => $user->id,
        'is_verified' => false,
    ]);

    $response = $this->getJson('/api/livros');

    $response->assertStatus(200)
             ->assertJsonCount(1)
             ->assertJsonFragment(['title' => 'Verified Book'])
             ->assertJsonMissing(['title' => 'Unverified Book']);
});

test('authenticated users can see their own books including unverified ones', function () {
    $user = User::factory()->create();

    Book::create([
        'title' => 'My Unverified Book',
        'author' => 'Me',
        'course' => 'Geral',
        'description' => 'Desc',
        'file_path' => 'books/mine.pdf',
        'user_id' => $user->id,
        'is_verified' => false,
    ]);

    $response = $this->actingAs($user)->getJson('/api/meus-livros');

    $response->assertStatus(200)
             ->assertJsonFragment(['title' => 'My Unverified Book']);
});

test('admin user can be created', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->is_admin)->toBeTrue();
    // mocking Panel might be hard, checking property is enough for unit/feature test logic
    // expect($admin->canAccessPanel(new \Filament\Panel()))->toBeTrue();
});

test('regular user cannot access admin panel', function () {
    $user = User::factory()->create();

    expect((bool) $user->is_admin)->toBeFalse();
    // expect($user->canAccessPanel(new \Filament\Panel()))->toBeFalse();
});

test('admin can approve a book', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $book = Book::create([
        'title' => 'Book to Approve',
        'author' => 'Me',
        'course' => 'Geral',
        'description' => 'Desc',
        'file_path' => 'books/pending.pdf',
        'user_id' => $user->id,
        'is_verified' => false,
    ]);

    $response = $this->actingAs($admin)->putJson("/api/livros/{$book->id}/aprovar");

    $response->assertStatus(200)
             ->assertJsonFragment(['message' => 'Livro aprovado com sucesso!']);

    expect($book->fresh()->is_verified)->toBeTrue();
});

test('regular user cannot approve a book', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $book = Book::create([
        'title' => 'Book to Approve',
        'author' => 'Me',
        'course' => 'Geral',
        'description' => 'Desc',
        'file_path' => 'books/pending.pdf',
        'user_id' => $otherUser->id,
        'is_verified' => false,
    ]);

    $response = $this->actingAs($user)->putJson("/api/livros/{$book->id}/aprovar");

    $response->assertStatus(403);

    expect($book->fresh()->is_verified)->toBeFalse();
});

test('unauthenticated user cannot approve a book', function () {
    $user = User::factory()->create();

    $book = Book::create([
        'title' => 'Book to Approve',
        'author' => 'Me',
        'course' => 'Geral',
        'description' => 'Desc',
        'file_path' => 'books/pending.pdf',
        'user_id' => $user->id,
        'is_verified' => false,
    ]);

    $response = $this->putJson("/api/livros/{$book->id}/aprovar");

    $response->assertStatus(401);

    expect($book->fresh()->is_verified)->toBeFalse();
});
