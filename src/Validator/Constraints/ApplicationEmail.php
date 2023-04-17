<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ApplicationEmail extends Constraint
{
    public string $message = 'En søknad med {{ email }} har allerede blitt registert';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
