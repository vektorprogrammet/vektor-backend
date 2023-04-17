<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class InfoMeeting extends Constraint
{
    public string $message = 'Infomøtet må ha en dato for å kunne vises på nettsiden';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
