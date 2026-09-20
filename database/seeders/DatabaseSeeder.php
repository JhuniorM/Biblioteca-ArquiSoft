<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Libro;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $categorias = collect([
            'Clasicos',
            'Literatura contemporanea',
            'Fantasia',
            'Ensayo',
        ])->mapWithKeys(fn ($nombre) => [
            $nombre => Categoria::updateOrCreate(['nombre' => $nombre]),
        ]);

        $libros = [
            ['titulo' => 'El Principito', 'autor' => 'Antoine de Saint-Exupery', 'isbn' => '9780156012195', 'categoria' => 'Clasicos', 'disponibles' => 3],
            ['titulo' => 'Cien Anos de Soledad', 'autor' => 'Gabriel Garcia Marquez', 'isbn' => '9780307474728', 'categoria' => 'Literatura contemporanea', 'disponibles' => 2],
            ['titulo' => 'Don Quijote de la Mancha', 'autor' => 'Miguel de Cervantes', 'isbn' => '9780060934347', 'categoria' => 'Clasicos', 'disponibles' => 1],
            ['titulo' => 'La Metamorfosis', 'autor' => 'Franz Kafka', 'isbn' => '9780553213690', 'categoria' => 'Clasicos', 'disponibles' => 0],
            ['titulo' => 'El Alquimista', 'autor' => 'Paulo Coelho', 'isbn' => '9780062315007', 'categoria' => 'Literatura contemporanea', 'disponibles' => 4],
            ['titulo' => 'El Peregrino de Compostela', 'autor' => 'Paulo Coelho', 'isbn' => '9780062511409', 'categoria' => 'Literatura contemporanea', 'disponibles' => 2],
            ['titulo' => 'Veronika Decide Morir', 'autor' => 'Paulo Coelho', 'isbn' => '9780061124262', 'categoria' => 'Literatura contemporanea', 'disponibles' => 1],
            ['titulo' => 'La Loca de la Casa', 'autor' => 'Rosa Montero', 'isbn' => '9788432210277', 'categoria' => 'Ensayo', 'disponibles' => 2],
            ['titulo' => 'Harry Potter y la Piedra Filosofal', 'autor' => 'J. K. Rowling', 'isbn' => '9780590353427', 'categoria' => 'Fantasia', 'disponibles' => 3],
            ['titulo' => 'Moby Dick', 'autor' => 'Herman Melville', 'isbn' => '9780142437247', 'categoria' => 'Clasicos', 'disponibles' => 0],
            ['titulo' => 'Alicia a Traves del Espejo', 'autor' => 'Lewis Carroll', 'isbn' => '9781503222687', 'categoria' => 'Fantasia', 'disponibles' => 2],
            ['titulo' => 'El Codigo Da Vinci', 'autor' => 'Dan Brown', 'isbn' => '9780307474278', 'categoria' => 'Literatura contemporanea', 'disponibles' => 1],
        ];

        foreach ($libros as $libro) {
            Libro::updateOrCreate(
                ['isbn' => $libro['isbn']],
                [
                    'categoria_id' => $categorias[$libro['categoria']]->id,
                    'titulo' => $libro['titulo'],
                    'autor' => $libro['autor'],
                    'ejemplares_totales' => max($libro['disponibles'], 4),
                    'ejemplares_disponibles' => $libro['disponibles'],
                ],
            );
        }
    }
}
