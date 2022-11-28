<?php

declare(strict_types=1);

namespace App\Domain\Model\Invoice;

enum DocumentType: int
{
    case Invoice = 1;

    case CreditNote = 2;

    case DebitNote = 3;
}
