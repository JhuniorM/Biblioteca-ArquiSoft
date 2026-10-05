<?php

namespace App\Services;

class DemoPaymentGateway
{
    /**
     * @param  array{metodo_pago: string, numero_tarjeta?: string|null}  $datos
     * @return array{aprobado: bool, mensaje: string}
     */
    public function autorizar(array $datos): array
    {
        $numeroTarjeta = $datos['numero_tarjeta'] ?? null;

        if ($datos['metodo_pago'] === 'tarjeta' && $numeroTarjeta !== null && str_ends_with($numeroTarjeta, '0002')) {
            return [
                'aprobado' => false,
                'mensaje' => 'La entidad bancaria rechazó el pago de demostración.',
            ];
        }

        return ['aprobado' => true, 'mensaje' => 'Pago autorizado.'];
    }
}