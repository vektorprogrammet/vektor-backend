<?php

namespace App\Controller;

use App\Entity\AccessRule;
use App\Entity\UnhandledAccessRule;
use App\Form\Type\AccessRuleType;
use App\Form\Type\RoutingAccessRuleType;
use App\Role\ReversedRoleHierarchy;
use App\Role\Roles;
use App\Service\AccessControlService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessRuleController extends AbstractController
{

    private AccessControlService $accessControlService;
    private ReversedRoleHierarchy $reversedRoleHierarchy;
    private ManagerRegistry $doctrine;

    public function __construct(AccessControlService $accessControlService,
                                ReversedRoleHierarchy $reversedRoleHierarchy,
                                ManagerRegistry $doctrine)
    {
        $this->accessControlService = $accessControlService;
        $this->reversedRoleHierarchy = $reversedRoleHierarchy;
        $this->doctrine = $doctrine;
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $customRules = $this->doctrine->getRepository(AccessRule::class)->findCustomRules();
        $routingRules = $this->doctrine->getRepository(AccessRule::class)->findRoutingRules();
        $unhandledRules = $this->doctrine->getRepository(UnhandledAccessRule::class)->findAll();
        return $this->render('admin/access_rule/index.html.twig', array(
            'customRules' => $customRules,
            'routingRules' => $routingRules,
            'unhandledRules' => $unhandledRules
        ));
    }

    /**
     * @param Request $request
     * @param AccessRule|null $accessRule
     * @return Response
     */
    public function createRule(Request $request, AccessRule $accessRule = null): Response
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([ Roles::TEAM_MEMBER ]);
        $form = $this->createForm(AccessRuleType::class, $accessRule, [
            'roles' => $roles
        ]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash("success", "Access rule created");
            } else {
                $this->addFlash("success", "Access rule edited");
            }
            
            return $this->redirectToRoute("access_rules_show");
        }
        return $this->render('admin/access_rule/create.html.twig', array(
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate
        ));
    }

    /**
     * @param Request $request
     * @param AccessRule|null $accessRule
     * @return Response
     */
    public function createRoutingRule(Request $request, AccessRule $accessRule = null): Response
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([ Roles::TEAM_MEMBER ]);
        $routes = $this->accessControlService->getRoutes();
        $form = $this->createForm(RoutingAccessRuleType::class, $accessRule, [
            'routes' => $routes,
            'roles' => $roles
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accessRule->setIsRoutingRule(true);
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash("success", "Access rule created");
            } else {
                $this->addFlash("success", "Access rule edited");
            }

            return $this->redirectToRoute("access_rules_show");
        }
        return $this->render('admin/access_rule/create.html.twig', array(
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate
        ));
    }

    /**
     * @param Request $request
     * @param AccessRule $rule
     * @return Response
     */
    public function copyAccessRule(Request $request, AccessRule $rule): Response
    {
        $clone = clone $rule;
        if ($rule->isRoutingRule()) {
            return $this->createRoutingRule($request, $clone);
        }
        
        return $this->createRule($request, $clone);
    }

    /**
     * @param AccessRule $accessRule
     * @return Response
     */
    public function delete(AccessRule $accessRule): Response
    {
        $em = $this->doctrine->getManager();
        $em->remove($accessRule);
        $em->flush();

        $this->addFlash("success", $accessRule->getName()." removed");

        return $this->redirectToRoute("access_rules_show");
    }
}
