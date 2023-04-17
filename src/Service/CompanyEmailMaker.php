<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompanyEmailMaker
{
    final public const EMAIL_DOMAIN = '@vektorprogrammet.no';

    /**
     * CompanyEmailMaker constructor.
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly LogService $logger)
    {
    }

    public function setCompanyEmailFor(User $user, $blackList): ?string
    {
        $allCompanyEmails = $this->em->getRepository(User::class)->findAllCompanyEmails();
        $allEmails = array_merge($allCompanyEmails, $blackList);
        $firstName = mb_strtolower((string) $this->replaceNorwegianCharacters($user->getFirstName()));
        $fullName = mb_strtolower((string) $this->replaceNorwegianCharacters($user->getFullName()));

        // self::EMAIL_DOMAIN is constant @vektorprogrammet.no
        $email = preg_replace('/\s+/', '.', $firstName) . self::EMAIL_DOMAIN;
        if (in_array($email, $allEmails, true)) {
            $email = preg_replace('/\s+/', '.', $fullName) . self::EMAIL_DOMAIN;
        }

        $i = 2;
        while (in_array($email, $allEmails, true)) {
            $email = preg_replace('/\s+/', '.', $fullName) . $i . self::EMAIL_DOMAIN;
            ++$i;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->logger->alert("Failed to create email for $user. Invalid email: $email");

            return null;
        }

        $user->setCompanyEmail($email);
        $this->em->flush();
        $this->logger->info("Created company email, $email, for $user");

        return $email;
    }

    private function replaceNorwegianCharacters($string)
    {
        setlocale(LC_ALL, 'nb_NO');

        // Converts accents and norwegian characters
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', (string) $string);

        // Removes ' and `after iconv(), and other invalid characters
        $string = preg_replace('/[^A-Za-z0-9 ]/', '', $string);

        return $string;
    }
}
