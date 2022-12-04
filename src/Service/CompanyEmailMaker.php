<?php


namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompanyEmailMaker
{
    private EntityManagerInterface $em;
    private LogService $logger;

    /**
     * CompanyEmailMaker constructor
     */
    public function __construct(EntityManagerInterface $em, LogService $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function setCompanyEmailFor(User $user, $blackList): ?string
    {
        $allCompanyEmails = $this->em->getRepository(User::class)->findAllCompanyEmails();
        $allEmails = array_merge($allCompanyEmails, $blackList);
        $firstName = strtolower($this->replaceNorwegianCharacters($user->getFirstName()));
        $fullName = strtolower($this->replaceNorwegianCharacters($user->getFullName()));


        $email = preg_replace('/\s+/', '.', $firstName) . '@vektorprogrammet.no';
        if (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName) . '@vektorprogrammet.no';
        }

        $i = 2;
        while (array_search($email, $allEmails) !== false) {
            $email = preg_replace('/\s+/', '.', $fullName) . $i .'@vektorprogrammet.no';
            $i++;
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
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // Removes ' and `after iconv(), and other invalid characters
        $string = preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        return $string;
    }
}
