// English values are the wire format (API/DB); Portuguese strings here are
// purely for display — this is the one place that translation happens.

export const statusLabels = {
    pending: 'Pendente',
    paid: 'Pago',
    received: 'Recebido',
    overdue: 'Vencido',
    cancelled: 'Cancelado',
};

export function statusLabel(status) {
    return statusLabels[status] ?? status;
}

export const partyTypeLabels = {
    individual: 'Pessoa Física',
    company: 'Empresa',
};

export function partyTypeLabel(type) {
    return partyTypeLabels[type] ?? type;
}
