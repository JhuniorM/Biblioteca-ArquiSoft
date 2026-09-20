<?php

use App\Http\Controllers\CatalogoController;
use App\Models\Categoria;
use App\Models\Libro;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio', [
        'libros' => Libro::query()
            ->with('categoria')
            ->orderBy('id')
            ->limit(4)
            ->get(),
    ]);
});

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/pages/catalogo.html', [CatalogoController::class, 'index'])->name('catalogo.legacy');
Route::get('/catalogo/{libro}', [CatalogoController::class, 'show'])->name('catalogo.show');
Route::get('/categorias', function () {
    return view('categorias', [
        'categorias' => Categoria::query()->withCount('libros')->orderBy('nombre')->get(),
    ]);
})->name('categorias.index');

Route::get('/{seccion}', function (string $seccion) {
    abort_unless(in_array($seccion, ['como-funciona', 'suscripciones', 'nosotros', 'contacto'], true), 404);

    return view('informativa', [
        'seccion' => $seccion,
    ]);
})->where('seccion', 'como-funciona|suscripciones|nosotros|contacto')->name('seccion');
