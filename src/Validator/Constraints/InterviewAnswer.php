<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InterviewAnswer extends Constraint
{
    public string $message = 'Dette feltet kan ikke være tomt';
}
