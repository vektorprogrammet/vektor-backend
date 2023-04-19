<?php

namespace App\Event;

use App\Entity\Receipt;
use Symfony\Contracts\EventDispatcher\Event;

class ReceiptEvent extends Event
{
    final public const CREATED = 'receipt.created';
    final public const REFUNDED = 'receipt.refunded';
    final public const REJECTED = 'receipt.rejected';
    final public const PENDING = 'receipt.pending';
    final public const EDITED = 'receipt.edited';
    final public const DELETED = 'receipt.deleted';

    public function __construct(private readonly Receipt $receipt)
    {
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }
}
