<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Libro;

#[Fillable(['nombre', 'descripcion'])]
class Categoria extends Model
{
    public function libros(): HasMany
    {
        return $this->hasMany(Libro::class);
    }
}