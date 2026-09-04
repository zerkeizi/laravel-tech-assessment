<?php

namespace App\Enums;

enum ReceivableStatus: string
{
    case Pending = 'pending';
    case Received = 'received';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';
}
