<?php

namespace App\EventSubscriber;

use App\Event\TeamEvent;
use App\Event\TeamMembershipEvent;
use App\Event\UserEvent;
use App\Google\GoogleAPI;
use App\Google\GoogleDrive;
use App\Google\GoogleGroups;
use App\Google\GoogleUsers;
use App\Service\CompanyEmailMaker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GSuiteSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private GoogleAPI $googleAPI;
    private CompanyEmailMaker $emailMaker;
    private GoogleUsers $userService;
    private GoogleGroups $groupService;
    private GoogleDrive $driveService;

    public function __construct(
        LoggerInterface $logger,
        GoogleAPI $googleAPI,
        CompanyEmailMaker $emailMaker,
        GoogleUsers $userService,
        GoogleGroups $groupService,
        GoogleDrive $driveService
    ) {
        $this->logger = $logger;
        $this->googleAPI = $googleAPI;
        $this->emailMaker = $emailMaker;
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->driveService = $driveService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            TeamMembershipEvent::CREATED => [
                ['createGSuiteUser', 1],
                ['addGSuiteUserToTeam', -1],
            ],
            TeamMembershipEvent::EDITED => [
                ['createGSuiteUser', 1],
                ['addGSuiteUserToTeam', 0],
                ['removeGSuiteUserFromTeam', -1],
            ],
            TeamMembershipEvent::DELETED => [
                ['removeGSuiteUserFromTeam', 0],
            ],
            TeamMembershipEvent::EXPIRED => [
                ['removeGSuiteUserFromTeam', 0],
            ],
            UserEvent::EDITED => [
                ['updateGSuiteUser', 0],
            ],
            UserEvent::COMPANY_EMAIL_EDITED => [
                ['updateGSuiteUser', 0],
            ],
            TeamEvent::CREATED => [
                ['createGSuiteTeam', 1],
                ['createGSuiteTeamDrive', -1],
            ],
            TeamEvent::EDITED => [
                ['editGSuiteTeam', 0],
            ],
        ];
    }

    public function createGSuiteUser(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $companyEmail = $user->getCompanyEmail();

        if ($this->userExists($companyEmail)) {
            return;
        }

        if (!$companyEmail) {
            $emailsInUse = $this->googleAPI->getAllEmailsInUse();
            $this->emailMaker->setCompanyEmailFor($user, $emailsInUse);
        }

        if (null !== $user->getCompanyEmail()) {
            $this->userService->createUser($user);
            $this->logger->info("New G Suite account created for *{$user}* with email *{$user->getCompanyEmail()}*");
        }
    }

    public function addGSuiteUserToTeam(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $team = $event->getTeamMembership()->getTeam();
        $department = $user->getDepartment();

        $alreadyInGroup = $this->groupService->userIsInGroup($user, $team);

        if (!$alreadyInGroup && $user->getCompanyEmail()) {
            $this->groupService->addUserToGroup($user, $team);
            $this->logger->info("$user added to G Suite group *$department - $team*");
        }
    }

    public function removeGSuiteUserFromTeam(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $team = $event->getTeamMembership()->getTeam();
        $department = $user->getDepartment();

        $activeTeamMemberships = $user->getActiveTeamMemberships();
        $shouldBeInGroup = false;

        foreach ($activeTeamMemberships as $activeTeamMembership) {
            if ($team === $activeTeamMembership->getTeam()) {
                $shouldBeInGroup = true;
                break;
            }
            $shouldBeInGroup = false;
        }

        if (!$shouldBeInGroup && $user->getCompanyEmail()) {
            $this->groupService->removeUserFromGroup($user, $team);
            $this->logger->info("$user removed from G Suite group *$department - $team*");
        }
    }

    public function updateGSuiteUser(UserEvent $event)
    {
        $user = $event->getUser();
        $oldEmail = $event->getOldEmail();
        if ($this->userExists($oldEmail)) {
            $this->userService->updateUser($oldEmail, $user);
            $this->logger
                ->info("G Suite account for *{$user}* with email *{$user->getCompanyEmail()}* has been updated.");
        }
    }

    public function createGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();

        if (!$this->teamExists($team)) {
            $this->groupService->createGroup($team);
            $this->logger->info("New G Suite group created for *$department - $team*");
        }
    }

    public function editGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();
        $oldEmail = $event->getOldTeamEmail();

        if (!$this->teamExists($oldEmail)) {
            $this->groupService->createGroup($team);
            $this->logger->info("New G Suite group created for *$department - $team*");
            $this->createGSuiteTeamDrive($event);
        } else {
            $this->groupService->updateGroup($oldEmail, $team);
            $this->logger->info("G Suite group for *$department - $team* has been updated");
        }
    }

    public function createGSuiteTeamDrive(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();

        if (!$this->teamExists($team)) {
            $this->driveService->createTeamDrive($team);
            $this->logger->info("New Team Drive created for *$department - $team*");
        }
    }

    private function userExists($email)
    {
        if (!$email) {
            return false;
        }

        return null !== $this->userService->getUser($email);
    }

    private function teamExists($email)
    {
        if (!$email) {
            return false;
        }

        return null !== $this->groupService->getGroup($email);
    }
}
