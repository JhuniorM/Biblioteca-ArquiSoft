<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['prestamo_id', 'multa_id', 'registrado_por_user_id', 'confirmado_por_user_id', 'confirmado_en', 'monto_centavos', 'moneda', 'metodo_pago', 'ultimos_digitos', 'proveedor', 'estado', 'referencia'])]
class Pago extends Model
{
    protected function casts(): array
    {
        return ['confirmado_en' => 'datetime'];
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function comprobante(): HasOne
    {
        return $this->hasOne(Comprobante::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por_user_id');
    }

    public function confirmadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmado_por_user_id');
    }
}