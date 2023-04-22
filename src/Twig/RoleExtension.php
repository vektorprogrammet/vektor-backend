<?php

namespace App\Twig;

use App\Entity\User;
use App\Role\Roles;
use App\Service\RoleManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RoleExtension extends AbstractExtension
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RoleManager $roleManager
    ) {
    }

    public function getName(): string
    {
        return 'role_extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_granted_assistant', $this->isGrantedAssistant(...)),
            new TwigFunction('is_granted_team_member', $this->isGrantedTeamMember(...)),
            new TwigFunction('is_granted_team_leader', $this->isGrantedTeamLeader(...)),
            new TwigFunction('is_granted_admin', $this->isGrantedAdmin(...)),
            new TwigFunction('user_is_granted_assistant', $this->userIsGrantedAssistant(...)),
            new TwigFunction('user_is_granted_team_member', $this->userIsGrantedTeamMember(...)),
            new TwigFunction('user_is_granted_team_leader', $this->userIsGrantedTeamLeader(...)),
            new TwigFunction('user_is_granted_admin', $this->userIsGrantedAdmin(...)),
            new TwigFunction('user_is_in_executive_board', $this->userIsInExecutiveBoard(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('get_role_name', $this->getRoleName(...)),
        ];
    }

    public function getRoleName($role): string
    {
        return Roles::GetRoleName($role);
    }

    public function isGrantedAssistant(): bool
    {
        return $this->isGranted(Roles::ASSISTANT);
    }

    public function isGrantedTeamMember(): bool
    {
        return $this->isGranted(Roles::TEAM_MEMBER);
    }

    public function isGrantedTeamLeader(): bool
    {
        return $this->isGranted(Roles::TEAM_LEADER);
    }

    public function isGrantedAdmin(): bool
    {
        return $this->isGranted(Roles::ADMIN);
    }

    private function isGranted(string $role): bool
    {
        if ($this->tokenStorage->getToken() === null) {
            return false;
        }

        return $this->authorizationChecker->isGranted($role);
    }

    public function userIsGrantedAssistant(User $user): bool
    {
        return $this->roleManager->userIsGranted($user, Roles::ASSISTANT);
    }

    public function userIsGrantedTeamMember(User $user): bool
    {
        return $this->roleManager->userIsGranted($user, Roles::TEAM_MEMBER);
    }

    public function userIsGrantedTeamLeader(User $user): bool
    {
        return $this->roleManager->userIsGranted($user, Roles::TEAM_LEADER);
    }

    public function userIsGrantedAdmin(User $user): bool
    {
        return $this->roleManager->userIsGranted($user, Roles::ADMIN);
    }

    public function userIsInExecutiveBoard(User $user): bool
    {
        return $this->roleManager->userIsInExecutiveBoard($user);
    }
}
