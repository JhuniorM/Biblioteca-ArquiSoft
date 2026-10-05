<?php

return [
    'dias_prestamo' => (int) env('BIBLIOTECA_DIAS_PRESTAMO', 15),
    'precio_alquiler_centavos' => (int) env('BIBLIOTECA_PRECIO_ALQUILER_CENTAVOS', 1000),
    'multa_por_dia_centavos' => (int) env('BIBLIOTECA_MULTA_POR_DIA_CENTAVOS', 100),
    'cajero' => [
        'nombre' => env('BIBLIOTECA_CAJERO_NOMBRE', 'Cajero ArquiSoft'),
        'email' => env('BIBLIOTECA_CAJERO_EMAIL'),
        'password' => env('BIBLIOTECA_CAJERO_PASSWORD'),
    ],
];
