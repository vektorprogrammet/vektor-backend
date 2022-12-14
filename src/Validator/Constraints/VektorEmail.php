<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VektorEmail extends Constraint
{
    public string $message = 'E-postadressen må slutte med "@vektorprogrammet.no"';
}
