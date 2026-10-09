<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
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
    public function getPortadaUrlAttribute(): ?string
    {
        $slug = Str::slug($this->titulo);
        $portadas = [
            'el-principito' => 'principito.jpg',
            'cien-anos-de-soledad' => 'cien.jpg',
            'don-quijote-de-la-mancha' => 'quijote.jpg',
            'harry-potter-y-la-piedra-filosofal' => 'harry-potter.jpg',
            'alicia-a-traves-del-espejo' => 'alicia-a-travez-del-espejo.jpg',
            'el-coronel-no-tiene-quien-le-escriba' => 'el-coronel-no-tiene-quien-le-escriba-garcia-marquez.jpg',
            'el-peso-del-corazon' => 'el-peso-del-corazon_rosa-montero.jpg',
            'la-ciudad-y-los-perros' => 'la-ciudad-y-los-perros-mario-vargas.jpg',
            'la-psicologia-del-dinero' => 'la-psicologia-del-dinero-morgan-housel.jpg',
            'la-sombra-del-viento' => 'la-sombra-del-viento-aniversario_carlos-ruiz-zafon.jpg',
            'los-hombres-del-norte' => 'los-hombre-del-norte.jpg',
            'ya-te-dije-adios-ahora-como-te-olvido' => 'ya-te-dije-adios-ahora-como-te-olvido_walter-riso.jpg',
            'las-islas-de-iros-el-reino-de-belmar' => 'portada_las-islas-de-iros-el-reino-de-belmar__202504290059.jpg',
            'fabulas-de-esopo' => 'portada_fabulas_varios-autores_202412221650.jpg',
            'recupera-tu-mente-reconquista-tu-vida' => 'recupera-tu-mente-reconquista-tu-vida_marian-rojas-estape.jpg',
            'volar-sobre-el-pantano' => 'volar-sobre-el-pantano-carlos-cuauhtemoc.jpg',
            'confieso-que-he-vivido' => 'confieso-que-he-vivido-por-pablo-neruda.jpg',
        ];
        $filename = $portadas[$slug] ?? "{$slug}.jpg";

        if (! is_file(public_path("libros/{$filename}"))) {
            return null;
        }

        return asset("libros/{$filename}");
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}