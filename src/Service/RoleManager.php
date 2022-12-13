<?php

namespace App\Service;

use App\Entity\ExecutiveBoardMembership;
use App\Entity\Semester;
use App\Entity\User;
use App\Google\GoogleUsers;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RoleManager
{
    private array $roles = [];
    private array $aliases = [];
    private AuthorizationCheckerInterface $authorizationChecker;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private GoogleUsers $googleUserService;

    /**
     * RoleManager constructor.
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        GoogleUsers $googleUserService
    ) {
        $this->roles = [
            Roles::ASSISTANT,
            Roles::TEAM_MEMBER,
            Roles::TEAM_LEADER,
            Roles::ADMIN,
        ];
        $this->aliases = [
            Roles::ALIAS_ASSISTANT,
            Roles::ALIAS_TEAM_MEMBER,
            Roles::ALIAS_TEAM_LEADER,
            Roles::ALIAS_ADMIN,
        ];
        $this->authorizationChecker = $authorizationChecker;
        $this->em = $em;
        $this->logger = $logger;
        $this->googleUserService = $googleUserService;
    }

    public function isValidRole(string $role): bool
    {
        return in_array($role, $this->roles, true) || in_array($role, $this->aliases, true);
    }

    public function canChangeToRole(string $role): bool
    {
        return
            $role !== Roles::ADMIN &&
            $role !== Roles::ALIAS_ADMIN &&
            $this->isValidRole($role)
        ;
    }

    public function mapAliasToRole(string $alias): string
    {
        if (in_array($alias, $this->roles, true)) {
            return $alias;
        }

        if (in_array($alias, $this->aliases, true)) {
            return $this->roles[array_search($alias, $this->aliases, true)];
        }
        throw new \InvalidArgumentException('Invalid alias: ' . $alias);
    }

    public function loggedInUserCanCreateUserWithRole(string $role): bool
    {
        if (!$this->isValidRole($role)) {
            return false;
        }

        $role = $this->mapAliasToRole($role);

        // Can't create admins
        // Only team leaders and admins can create users with higher permissions than ASSISTANT
        return
            $role !== Roles::ADMIN &&
            !(!$this->authorizationChecker->isGranted(Roles::TEAM_LEADER) &&
                $role !== Roles::ASSISTANT)
        ;
    }

    public function loggedInUserCanChangeRoleOfUsersWithRole(User $user, string $role): bool
    {
        // Teamleaders can't change the role of admins
        $loggedInAsAdmin = $this->authorizationChecker->isGranted(Roles::ADMIN);
        $tryingToChangeAdmin = $this->userIsGranted($user, Roles::ADMIN);

        return
            ($loggedInAsAdmin || !$tryingToChangeAdmin) &&
            $this->canChangeToRole($role);
    }

    public function userIsGranted(User $user, string $role): bool
    {
        $roles = [
            Roles::ASSISTANT,
            Roles::TEAM_MEMBER,
            Roles::TEAM_LEADER,
            Roles::ADMIN,
        ];

        if (empty($user->getRoles())) {
            return false;
        }

        $userRole = $user->getRoles()[0];

        $userAccessLevel = array_search($userRole, $roles, true);
        $roleAccessLevel = array_search($role, $roles, true);

        return $userAccessLevel >= $roleAccessLevel;
    }

    /**
     * @return bool True if role was updated, false if no role changed
     */
    public function updateUserRole(User $user): bool
    {
        if ($this->userIsInExecutiveBoard($user) || $this->userIsTeamLeader($user)) {
            $updated = $this->setUserRole($user, Roles::TEAM_LEADER);
        } elseif ($this->userIsTeamMember($user)) {
            $updated = $this->setUserRole($user, Roles::TEAM_MEMBER);
        } else {
            $updated = $this->setUserRole($user, Roles::ASSISTANT);
        }

        if ($updated && $user->getCompanyEmail()) {
            $shouldSuspendGoogleUser = !$this->userIsGranted($user, Roles::TEAM_MEMBER);
            $this->googleUserService->updateUser($user->getCompanyEmail(), $user, $shouldSuspendGoogleUser);
        }

        return $updated;
    }

    public function userIsInExecutiveBoard(User $user): bool
    {
        $executiveBoardMembership = $this->em->getRepository(ExecutiveBoardMembership::class)->findByUser($user);

        return !empty($executiveBoardMembership);
    }

    private function userIsTeamLeader(User $user): bool
    {
        return $this->userIsInATeam($user, true);
    }

    private function userIsTeamMember(User $user): bool
    {
        return $this->userIsInATeam($user, false);
    }

    private function userIsInATeam(User $user, bool $teamLeader): bool
    {
        $semester = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester();
        $teamMemberships = $user->getTeamMemberships();

        if ($semester === null) {
            return false;
        }

        foreach ($teamMemberships as $teamMembership) {
            if ($teamMembership->isActiveInSemester($semester) && $teamMembership->isTeamLeader() === $teamLeader) {
                return true;
            }
        }

        return false;
    }

    private function setUserRole(User $user, string $role): bool
    {
        $isValidRole = $this->isValidRole($role);
        if (!$isValidRole) {
            throw new \InvalidArgumentException("Invalid role $role");
        }
        if ($this->userIsGranted($user, Roles::ADMIN)) {
            return false;
        }

        $roleNeedsToUpdate = array_search($role, $user->getRoles(), true) === false;

        if ($roleNeedsToUpdate) {
            $user->setRoles([$role]);
            $this->em->flush();

            $this->logger->info("Automatic role update ({$user->getDepartment()}): $user has been updated to $role");

            return true;
        }

        return false;
    }
}
