<?php

namespace App\Service;

use App\Entity\User;
use App\Mailer\MailingInterface;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;

class UserRegistration
{
    /**
     * UserRegistration constructor.
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly EntityManagerInterface $em,
        private readonly MailingInterface $mailer
    ) {
    }

    public function setNewUserCode(User $user): string
    {
        $newUserCode = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedNewUserCode = hash('sha512', $newUserCode, false);
        $user->setNewUserCode($hashedNewUserCode);

        $this->em->persist($user);
        $this->em->flush();

        return $newUserCode;
    }

    public function createActivationEmail(User $user, $newUserCode): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->subject('Velkommen til Vektorprogrammet!')
            ->from('vektorprogrammet@vektorprogrammet.no', 'Vektorprogrammet')
            ->replyTo($user->getFieldOfStudy()->getDepartment()->getEmail())
            ->to($user->getEmail())
            ->htmlTemplate('new_user/create_new_user_email.html.twig')
            ->context([
                'newUserCode' => $newUserCode,
                'name' => $user->getFullName(),
            ]);
    }

    public function sendActivationCode(User $user): void
    {
        $newUserCode = $this->setNewUserCode($user);

        $this->mailer->send($this->createActivationEmail($user, $newUserCode));
    }

    public function getHashedCode(string $newUserCode): string
    {
        return hash('sha512', $newUserCode, false);
    }

    public function activateUserByNewUserCode(string $newUserCode): ?User
    {
        $hashedNewUserCode = $this->getHashedCode($newUserCode);
        $user = $this->em->getRepository(User::class)->findUserByNewUserCode($hashedNewUserCode);
        if ($user === null) {
            return null;
        }

        if ($user->getUserIdentifier() === null) {
            // Set default username to email
            $user->setUserName($user->getEmail());
        }

        $user->setNewUserCode(null);

        $user->setActive('1');

        if ((is_countable($user->getRoles()) ? count($user->getRoles()) : 0) === 0) {
            $role = Roles::ASSISTANT;
            $user->addRole($role);
        }

        return $user;
    }
}
