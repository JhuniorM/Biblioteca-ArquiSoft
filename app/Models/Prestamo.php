<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    protected $fillable = ['libro_id', 'user_id', 'fecha_prestamo', 'estado'];

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    public function pago(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Pago::class);
    }
}