<?php

return [
    'dias_prestamo' => (int) env('BIBLIOTECA_DIAS_PRESTAMO', 15),
    'multa_por_dia_centavos' => (int) env('BIBLIOTECA_MULTA_POR_DIA_CENTAVOS', 100),
];
