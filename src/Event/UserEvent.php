<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{
    const CREATED = 'user.created';
    const EDITED = 'user.edited';
    const DELETED = 'user.deleted';
    const COMPANY_EMAIL_EDITED = 'user.company_email_edited';

    private $user;
    private $oldEmail;

    /**
     * @param User $user
     * @param string $oldEmail
     */
    public function __construct(User $user, $oldEmail)
    {
        $this->user = $user;
        $this->oldEmail = $oldEmail;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getOldEmail()
    {
        return $this->oldEmail;
    }
}
