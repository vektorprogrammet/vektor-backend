<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    public const CREATED = 'user.created';
    public const EDITED = 'user.edited';
    public const DELETED = 'user.deleted';
    public const COMPANY_EMAIL_EDITED = 'user.company_email_edited';

    private User $user;
    private $oldEmail;

    public function __construct(User $user, $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
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
