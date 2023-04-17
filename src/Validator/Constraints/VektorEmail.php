<?php

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class VektorEmail extends Constraint
{
    public string $message = 'E-postadressen må slutte med "@vektorprogrammet.no"';
}
