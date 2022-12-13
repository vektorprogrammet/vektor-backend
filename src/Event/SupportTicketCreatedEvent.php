<?php

namespace App\Event;

use App\Entity\SupportTicket;
use Symfony\Contracts\EventDispatcher\Event;

class SupportTicketCreatedEvent extends Event
{
    public const NAME = 'support_ticket.created';

    private $supportTicket;

    public function __construct(SupportTicket $supportTicket)
    {
        $this->supportTicket = $supportTicket;
    }

    /**
     */
    public function getSupportTicket(): SupportTicket
    {
        return $this->supportTicket;
    }
}
