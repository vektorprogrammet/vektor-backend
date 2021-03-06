<?php

namespace App\Event;

use App\Entity\AssistantHistory;
use Symfony\Contracts\EventDispatcher\Event;

class AssistantHistoryCreatedEvent extends Event
{
    const NAME = 'assistant_history.created';

    private $assistantHistory;

    /**
     * ApplicationAdmissionEvent constructor.
     *
     * @param AssistantHistory $assistantHistory
     */
    public function __construct(AssistantHistory $assistantHistory)
    {
        $this->assistantHistory = $assistantHistory;
    }

    /**
     * @return AssistantHistory
     */
    public function getAssistantHistory(): AssistantHistory
    {
        return $this->assistantHistory;
    }
}
