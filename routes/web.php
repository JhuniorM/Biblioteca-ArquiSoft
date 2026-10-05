<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PrestamoController;
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
Route::get('/catalogo/{libro}/alquilar', [PagoController::class, 'create'])->name('pagos.create');
Route::post('/catalogo/{libro}/alquilar', [PagoController::class, 'store'])->name('pagos.store');
Route::get('/prestamos/{prestamo}/pago-exitoso', [PagoController::class, 'success'])->name('pagos.success');
Route::post('/prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver'])
    ->middleware(['auth', 'role:bibliotecario,administrador'])
    ->name('prestamos.devolver');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::get('/registro', [AuthController::class, 'register'])->name('register');
Route::post('/registro', [AuthController::class, 'storeRegistration'])->name('register.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/mis-prestamos', [PrestamoController::class, 'index'])->name('prestamos.index');
    Route::get('/mis-multas', [MultaController::class, 'index'])->name('multas.index');
    Route::post('/multas/{multa}/pagar', [MultaController::class, 'pagar'])->name('multas.pagar');
});
Route::get('/gestion/clientes', [PrestamoController::class, 'clientes'])
    ->middleware(['auth', 'role:bibliotecario,administrador'])
    ->name('prestamos.clientes');
Route::post('/gestion/clientes/prestamos', [PrestamoController::class, 'registrarParaCliente'])
    ->middleware(['auth', 'role:bibliotecario,administrador'])
    ->name('prestamos.clientes.registrar');
Route::post('/gestion/clientes/registrar', [PrestamoController::class, 'registrarCliente'])
    ->middleware(['auth', 'role:bibliotecario,administrador'])
    ->name('prestamos.clientes.crear');
Route::get('/gestion/prestamos', [PrestamoController::class, 'todos'])
    ->middleware(['auth', 'role:bibliotecario,administrador'])
    ->name('prestamos.todos');
Route::get('/gestion/pagos', [PagoController::class, 'gestion'])
    ->middleware(['auth', 'role:cajero,bibliotecario,administrador'])
    ->name('pagos.gestion');
Route::post('/gestion/pagos/{pago}/confirmar', [PagoController::class, 'confirmar'])
    ->middleware(['auth', 'role:cajero,administrador'])
    ->name('pagos.confirmar');
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
