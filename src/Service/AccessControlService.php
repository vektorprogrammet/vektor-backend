<?php

namespace App\Service;

use App\Entity\AccessRule;
use App\Entity\UnhandledAccessRule;
use App\Entity\User;
use App\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class AccessControlService
{
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private RoleManager $roleManager;
    private UserService $userService;
    private array $accessRulesCache;
    private array $unhandledRulesCache;

    /**
     * AccessControlService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        RoleManager $roleManager,
        UserService $userService
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->roleManager = $roleManager;
        $this->userService = $userService;
        $this->accessRulesCache = [];
        $this->unhandledRulesCache = [];
        $this->preloadCache();
    }

    private function preloadCache()
    {
        $accessRules = $this->entityManager->getRepository(AccessRule::class)->findAll();
        foreach ($accessRules as $rule) {
            $key = $this->getKey($rule->getResource(), $rule->getMethod());
            if (!\array_key_exists($key, $this->accessRulesCache)) {
                $this->accessRulesCache[$key] = [];
            }
            $this->accessRulesCache[$key][] = $rule;
        }

        $unhandledRules = $this->entityManager->getRepository(UnhandledAccessRule::class)->findAll();
        foreach ($unhandledRules as $rule) {
            $key = $this->getKey($rule->getResource(), $rule->getMethod());
            if (!\array_key_exists($key, $this->unhandledRulesCache)) {
                $this->unhandledRulesCache[$key] = [];
            }
            $this->unhandledRulesCache[$key][] = $rule;
        }
    }

    public function createRule(AccessRule $accessRule)
    {
        $em = $this->entityManager;
        $unhandledRules = $em->getRepository(UnhandledAccessRule::class)->findByResourceAndMethod($accessRule->getResource(), $accessRule->getMethod());
        foreach ($unhandledRules as $unhandledRule) {
            $em->remove($unhandledRule);
        }

        $em->persist($accessRule);
        $em->flush();

        $this->preloadCache();
    }

    public function checkAccess($resources, User $user = null): bool
    {
        if (null === $user) {
            $user = $this->getLoggedInUser();
        }

        if (\is_string($resources)) {
            $resource = $resources;

            return $this->checkAccessToResourceAndMethod($user, $resource);
        }

        if (!\is_array($resources)) {
            throw new \InvalidArgumentException();
        }

        foreach ($resources as $resource => $method) {
            $onlyRouteSpecified = is_numeric($resource);
            if ($onlyRouteSpecified) {
                $resource = $method;
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource);
            } else {
                $hasAccess = $this->checkAccessToResourceAndMethod($user, $resource, $method);
            }

            if (!$hasAccess) {
                return false;
            }
        }

        return true;
    }

    private function checkAccessToResourceAndMethod(?User $user, string $resource, string $method = 'GET'): bool
    {
        $accessRules = $this->getAccessRules($resource, $method);

        if (empty($accessRules)) {
            $this->markRuleAsUnhandledIfNotExists($resource, $method);
        }

        $everyoneHasAccess = !empty(array_filter($accessRules, function (AccessRule $rule) {
            return $rule->isEmpty();
        }));
        if (empty($accessRules) || $everyoneHasAccess) {
            return true;
        }

        if (null === $user || empty($user->getRoles())) {
            return false;
        }

        if ($this->roleManager->userIsGranted($user, Roles::ADMIN)) {
            return true;
        }

        foreach ($accessRules as $accessRule) {
            if ($this->userHasAccessToRule($user, $accessRule)) {
                return true;
            }
        }

        return false;
    }

    private function getLoggedInUser()
    {
        return $this->userService->getCurrentUser();
    }

    private function userHasAccessToRule(User $user, AccessRule $rule): bool
    {
        if (\count(is_countable($rule->getUsers()) ? $rule->getUsers() : []) > 0 && !($user->isActive() && $this->userIsInRuleUserList($user, $rule))) {
            return false;
        }

        if (!$this->userHasTeamOrExecutiveBoardAccessToRule($user, $rule)) {
            return false;
        }

        if (\count(is_countable($rule->getRoles()) ? $rule->getRoles() : []) > 0 && !$this->userRoleHasAccessToRule($user, $rule)) {
            return false;
        }

        return true;
    }

    private function userHasTeamOrExecutiveBoardAccessToRule(User $user, AccessRule $rule): bool
    {
        $teamRule = \count(is_countable($rule->getTeams()) ? $rule->getTeams() : []) > 0;
        $executiveRule = $rule->isForExecutiveBoard();
        $hasTeamAccess = $this->userHasTeamAccessToRule($user, $rule);
        $hasExecutiveBoardAccess = \count(is_countable($user->getActiveExecutiveBoardMemberships()) ? $user->getActiveExecutiveBoardMemberships() : []) > 0;
        if ($teamRule && $executiveRule && !($hasTeamAccess || $hasExecutiveBoardAccess)) {
            return false;
        } elseif ($teamRule && !$executiveRule && !$hasTeamAccess) {
            return false;
        } elseif ($executiveRule && !$teamRule && !$hasExecutiveBoardAccess) {
            return false;
        }

        return true;
    }

    private function userHasTeamAccessToRule(User $user, AccessRule $rule): bool
    {
        if (empty($rule->getTeams())) {
            return false;
        }

        foreach ($user->getActiveTeamMemberships() as $membership) {
            foreach ($rule->getTeams() as $team) {
                if ($membership->getTeam() === $team) {
                    return true;
                }
            }
        }

        return false;
    }

    private function userIsInRuleUserList(User $user, AccessRule $rule): bool
    {
        foreach ($rule->getUsers() as $userInRule) {
            if ($user === $userInRule) {
                return true;
            }
        }

        return false;
    }

    private function userRoleHasAccessToRule(User $user, AccessRule $rule): bool
    {
        foreach ($rule->getRoles() as $roleInRule) {
            foreach ($user->getRoles() as $userRole) {
                if ($roleInRule === $userRole) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getRoutes(): array
    {
        $resources = $this->router->getRouteCollection()->all();
        $resources = array_filter($resources, function ($v, string $resource) {
            return '' !== $resource && !$this->isPrivateRoute($resource);
        }, \ARRAY_FILTER_USE_BOTH);

        uasort($resources, function (Route $a, Route $b) {
            if ($this->isControlPanelRoute($a) && !$this->isControlPanelRoute($b)) {
                return -1;
            }
            if ($this->isControlPanelRoute($b) && !$this->isControlPanelRoute($a)) {
                return 1;
            }

            return strcmp($a->getPath(), $b->getPath());
        });

        return $resources;
    }

    private function isControlPanelRoute(Route $resource): bool
    {
        return '/kontrollpanel' === mb_substr($resource->getPath(), 0, 14);
    }

    private function isPrivateRoute(string $route): bool
    {
        return
            '' !== $route &&
            '_' === mb_substr($route, 0, 1) &&
            $this->isRoute($route);
    }

    public function getPath(string $name): string
    {
        if (!$this->isRoute($name)) {
            return $name;
        }

        return $this->router->getRouteCollection()->get($name)->getPath();
    }

    private function isRoute(string $name): bool
    {
        return null !== $this->router->getRouteCollection()->get($name);
    }

    private function markRuleAsUnhandledIfNotExists(string $resource, string $method = 'GET')
    {
        if ($this->isPrivateRoute($resource) || $this->unhandledRuleExists($resource, $method)) {
            return;
        }

        $this->entityManager->persist(new UnhandledAccessRule($resource, $method));
        $this->entityManager->flush();

        $this->preloadCache();
    }

    private function unhandledRuleExists(string $resource, $method): bool
    {
        return !empty($this->getUnhandledRules($resource, $method));
    }

    private function getAccessRules(string $resource, string $method)
    {
        $key = $this->getKey($resource, $method);
        if (\array_key_exists($key, $this->accessRulesCache)) {
            return $this->accessRulesCache[$key];
        }

        return [];
    }

    private function getUnhandledRules(string $resource, string $method)
    {
        $key = $this->getKey($resource, $method);
        if (\array_key_exists($key, $this->unhandledRulesCache)) {
            return $this->unhandledRulesCache[$key];
        }

        return [];
    }

    private function getKey(string $resource, string $method): string
    {
        return "$method-$resource";
    }
}
