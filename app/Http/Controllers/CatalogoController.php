<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    /**
     * Display the catalog with optional search filters.
     */
    public function index(Request $request): View
    {
        $titulo = $request->string('titulo')->trim()->toString();
        $autor = $request->string('autor')->trim()->toString();
        $categoriaId = $request->integer('categoria_id');
        $disponibilidad = $request->string('disponibilidad')->toString();

        $libros = Libro::query()
            ->with('categoria')
            ->when($titulo !== '', function ($query) use ($titulo) {
                $query->where('titulo', 'like', "%{$titulo}%");
            })
            ->when($autor !== '', function ($query) use ($autor) {
                $query->where('autor', 'like', "%{$autor}%");
            })
            ->when($categoriaId > 0, function ($query) use ($categoriaId) {
                $query->where('categoria_id', $categoriaId);
            })
            ->when($disponibilidad === 'disponible', function ($query) {
                $query->where('ejemplares_disponibles', '>', 0);
            })
            ->when($disponibilidad === 'agotado', function ($query) {
                $query->where('ejemplares_disponibles', '=', 0);
            })
            ->orderBy('titulo')
            ->paginate(12)
            ->withQueryString();

        $categorias = Categoria::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('catalogo', [
            'libros' => $libros,
            'categorias' => $categorias,
            'titulo' => $titulo,
            'autor' => $autor,
            'categoriaId' => $categoriaId > 0 ? $categoriaId : null,
            'disponibilidad' => in_array($disponibilidad, ['disponible', 'agotado'], true) ? $disponibilidad : null,
        ]);
    }

    public function show(Libro $libro): View
    {
        return view('catalogo-detalle', [
            'libro' => $libro->load('categoria'),
        ]);
    }
}