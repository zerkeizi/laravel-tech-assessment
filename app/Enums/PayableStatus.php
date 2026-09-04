<?php

namespace App\Enums;

enum PayableStatus: string
{
    case Pendente = 'pendente';
    case Pago = 'pago';
    case Vencido = 'vencido';
    case Cancelado = 'cancelado';
}
