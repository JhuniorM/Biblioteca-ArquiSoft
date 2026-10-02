<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['prestamo_id', 'monto_centavos', 'dias_atraso', 'estado', 'pagada_en'])]
class Multa extends Model
{
    protected function casts(): array
    {
        return [
            'pagada_en' => 'datetime',
        ];
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }
}
