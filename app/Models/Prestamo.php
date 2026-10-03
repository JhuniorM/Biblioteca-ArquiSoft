<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prestamo extends Model
{
    protected $fillable = ['libro_id', 'user_id', 'fecha_prestamo', 'fecha_vencimiento', 'fecha_devolucion', 'devuelto_por_user_id', 'estado'];

    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'date',
            'fecha_vencimiento' => 'date',
            'fecha_devolucion' => 'date',
        ];
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function devueltoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'devuelto_por_user_id');
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    public function multa(): HasOne
    {
        return $this->hasOne(Multa::class);
    }
}
