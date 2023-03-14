<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCompanyEmail extends Constraint
{
    public string $message = 'E-posten "{{ email }}" er allerede i bruk';
}
