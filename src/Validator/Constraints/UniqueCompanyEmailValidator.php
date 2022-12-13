<?php

namespace App\Validator\Constraints;

use App\Entity\Team;
use App\Entity\User;
use App\Google\GoogleAPI;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCompanyEmailValidator extends ConstraintValidator
{
    private $em;
    private $googleAPI;

    public function __construct(EntityManagerInterface $em, GoogleAPI $googleAPI)
    {
        $this->em = $em;
        $this->googleAPI = $googleAPI;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value || !$this->objectHasChanged($value)) {
            return;
        }

        $googleEmails = $this->googleAPI->getAllEmailsInUse();
        $teamEmails = $this->em->getRepository(Team::class)->findAllEmails();
        $userCompanyEmails = $this->em->getRepository(User::class)->findAllCompanyEmails();
        $allEmails = array_merge($googleEmails, $teamEmails, $userCompanyEmails);

        if (false !== array_search($value, $allEmails, true)) {
            $this->context->buildViolation($constraint->message)
                          ->setParameter('{{ email }}', $value)
                          ->addViolation();
        }
    }

    private function objectHasChanged($value)
    {
        $object = $this->context->getObject();
        $oldObject = $this->em
            ->getUnitOfWork()
            ->getOriginalEntityData($object);

        if ($object instanceof User && \array_key_exists('companyEmail', $oldObject) && $oldObject['companyEmail'] === $value) {
            return false;
        } elseif ($object instanceof Team && \array_key_exists('email', $oldObject) && $oldObject['email'] === $value) {
            return false;
        }

        return true;
    }
}
