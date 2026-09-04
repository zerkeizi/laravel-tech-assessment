<?php

namespace App\Enums;

enum ReceivableStatus: string
{
    case Pendente = 'pendente';
    case Recebido = 'recebido';
    case Vencido = 'vencido';
    case Cancelado = 'cancelado';
}
