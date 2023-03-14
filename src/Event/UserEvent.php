<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    final public const CREATED = 'user.created';
    final public const EDITED = 'user.edited';
    final public const DELETED = 'user.deleted';
    final public const COMPANY_EMAIL_EDITED = 'user.company_email_edited';

    public function __construct(private readonly User $user, private $oldEmail)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOldEmail()
    {
        return $this->oldEmail;
    }
}
