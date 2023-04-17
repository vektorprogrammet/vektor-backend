<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueCompanyEmail extends Constraint
{
    public string $message = 'E-posten "{{ email }}" er allerede i bruk';
}
