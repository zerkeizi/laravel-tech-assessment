<?php

namespace App\Services;

use App\Models\Payable;
use App\Models\Receivable;

class DashboardService
{
    public function summary(): array
    {
        $totalAReceber = (float) Receivable::pending()->sum('amount');
        $totalRecebido = (float) Receivable::where('status', 'recebido')->sum('amount');
        $totalVencidoReceber = (float) Receivable::overdue()->sum('amount');

        $totalAPagar = (float) Payable::pending()->sum('amount');
        $totalPago = (float) Payable::where('status', 'pago')->sum('amount');
        $totalVencidoPagar = (float) Payable::overdue()->sum('amount');

        return [
            'total_a_receber' => $totalAReceber,
            'total_recebido' => $totalRecebido,
            'total_vencido_receber' => $totalVencidoReceber,
            'total_a_pagar' => $totalAPagar,
            'total_pago' => $totalPago,
            'total_vencido_pagar' => $totalVencidoPagar,
            'saldo_previsto' => $totalAReceber - $totalAPagar,
            'saldo_realizado' => $totalRecebido - $totalPago,
        ];
    }
}
