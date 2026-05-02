<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Book;

class RouteTest extends TestCase
{
 
    public function test_get_books(): void
    {
        $response = $this->get('/admin/books');

        $response->assertStatus(200);
    }

    public function test_post_book()
    {
        $this->withoutMiddleware();

        $response = $this->post('/admin/books', [
            'title' => 'Book test deleted',
            'total_pages' => 50
        ]);
        $response->assertStatus(201);
    }

    public function test_put_book()
    {
        $response = $this->withoutMiddleware()->put("/admin/books/1", [
            'title' => 'Book edited',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('books', [
            'id' => 1,
            'title' => 'Book edited',
        ]);
    }

    public function test_delete_book()
    {
        $response = $this->withoutMiddleware()->delete("/admin/books/13");

        $response->assertStatus(200);
        
        $this->assertDatabaseMissing('books', [
            'id' => 13,
        ]);
    }

    public function test_debe_subir_un_pdf_correctamente()
    {
        // 1. Simular el disco para no guardar archivos reales en tu PC
        Storage::fake('local');

        // 2. Crear un libro en la base de datos de pruebas para que la validación 'exists' pase
        $book = Book::create([
            'title' => 'Prueba para PDF',
            'total_pages' => 10
        ]);

        // 3. Crear un archivo PDF falso (nombre, tamaño en KB)
        $pdfFalso = UploadedFile::fake()->create('mi_libro.pdf', 500, 'application/pdf');

        // 4. Hacer la petición POST a la ruta
        // Usamos withoutMiddleware() porque tu ruta está en web.php y evitar el error 419 (CSRF)
        $response = $this->withoutMiddleware()->post('/admin/upload', [
            'book_id' => $book->id,
            'file' => $pdfFalso,
        ]);

        // 5. Verificaciones (Assertions)
        $response->assertStatus(200);
        
        // Verificamos que el JSON devuelva una ruta
        $response->assertJsonStructure(['path']);
        
        // Verificamos que el archivo realmente se guardó en el disco falso
        $pathRecibido = $response->json('path');
        Storage::disk('local')->assertExists($pathRecibido);
    }
}
