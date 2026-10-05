<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pago_id', 'numero', 'emitido_en'])]
class Comprobante extends Model
{
    protected function casts(): array
    {
        return ['emitido_en' => 'datetime'];
    }

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}