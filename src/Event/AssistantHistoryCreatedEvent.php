<?php

namespace App\Event;

use App\Entity\AssistantHistory;
use Symfony\Contracts\EventDispatcher\Event;

class AssistantHistoryCreatedEvent extends Event
{
    public const NAME = 'assistant_history.created';

    private AssistantHistory $assistantHistory;

    /**
     * ApplicationAdmissionEvent constructor.
     */
    public function __construct(AssistantHistory $assistantHistory)
    {
        $this->assistantHistory = $assistantHistory;
    }

    public function getAssistantHistory(): AssistantHistory
    {
        return $this->assistantHistory;
    }
}
