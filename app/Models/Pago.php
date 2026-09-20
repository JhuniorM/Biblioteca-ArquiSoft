<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['prestamo_id', 'monto_centavos', 'moneda', 'proveedor', 'estado', 'referencia'])]
class Pago extends Model
{
    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }
}