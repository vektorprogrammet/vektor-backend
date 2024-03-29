<?php

namespace App\EventSubscriber;

use App\Entity\AdmissionNotification;
use App\Entity\InterviewAnswer;
use App\Entity\InterviewQuestion;
use App\Entity\InterviewQuestionAlternative;
use App\Entity\InterviewScore;
use App\Entity\PasswordReset;
use App\Entity\Role;
use App\Entity\User;
use App\Role\Roles;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DbSubscriber implements EventSubscriber
{
    private array $ignoredClasses = [
        InterviewAnswer::class,
        InterviewQuestion::class,
        InterviewQuestionAlternative::class,
        InterviewScore::class,
        PasswordReset::class,
        AdmissionNotification::class,
    ];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $manager,
        string $env
    ) {
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     */
    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'postPersist',
            'postUpdate',
            'postRemove',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $obj = $args->getObject();

        if ($obj instanceof User) {
            $this->setDefaultUserRole($obj);
        }
    }

    private function setDefaultUserRole(User $user)
    {
        if (!empty($user->getRoles())) {
            return;
        }

        // TODO: fix next line, set to Role ASSISTANT
        $defaultRole = $this->manager->getRepository(Role::class)->findByRoleName(Roles::ASSISTANT);
        $user->addRole($defaultRole);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->log($args, 'Updated');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->log($args, 'Created');
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->log($args, 'Deleted');
    }

    private function log(LifecycleEventArgs $args, string $action)
    {
        $obj = $args->getObject();
        $className = $obj::class;

        if (in_array($className, $this->ignoredClasses, true)) {
            return;
        }

        $lastSlashIdx = mb_strrpos($className, '\\');
        if (false !== $lastSlashIdx) {
            $className = mb_substr($className, $lastSlashIdx + 1);
        }

        $objName = $this->getObjectName($obj);

        $this->logger->info("$action $className $objName");
    }

    private function getObjectName($obj)
    {
        $name = '';
        if (method_exists($obj, '__toString')) {
            $name = "*{$obj->__toString()}*";
        }

        return $name;
    }
}
