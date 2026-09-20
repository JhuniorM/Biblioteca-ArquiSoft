<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Categoria;

#[Fillable([
    'categoria_id',
    'titulo',
    'autor',
    'isbn',
    'editorial',
    'anio_publicacion',
    'ejemplares_totales',
    'ejemplares_disponibles',
])]
class Libro extends Model
{
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}