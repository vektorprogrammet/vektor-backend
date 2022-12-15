<?php

namespace App\Controller;

use App\Entity\AccessRule;
use App\Entity\UnhandledAccessRule;
use App\Form\Type\AccessRuleType;
use App\Form\Type\RoutingAccessRuleType;
use App\Role\ReversedRoleHierarchy;
use App\Role\Roles;
use App\Service\AccessControlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessRuleController extends AbstractController
{
    public function __construct(private readonly AccessControlService $accessControlService, private readonly ReversedRoleHierarchy $reversedRoleHierarchy)
    {
    }

    public function index(): Response
    {
        $customRules = $this->getDoctrine()->getRepository(AccessRule::class)->findCustomRules();
        $routingRules = $this->getDoctrine()->getRepository(AccessRule::class)->findRoutingRules();
        $unhandledRules = $this->getDoctrine()->getRepository(UnhandledAccessRule::class)->findAll();

        return $this->render('admin/access_rule/index.html.twig', [
            'customRules' => $customRules,
            'routingRules' => $routingRules,
            'unhandledRules' => $unhandledRules,
        ]);
    }

    public function createRule(Request $request, AccessRule $accessRule = null): Response
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $form = $this->createForm(AccessRuleType::class, $accessRule, [
            'roles' => $roles,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash('success', 'Access rule created');
            } else {
                $this->addFlash('success', 'Access rule edited');
            }

            return $this->redirectToRoute('access_rules_show');
        }

        return $this->render('admin/access_rule/create.html.twig', [
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate,
        ]);
    }

    public function createRoutingRule(Request $request, AccessRule $accessRule = null): Response
    {
        if ($isCreate = $accessRule === null) {
            $accessRule = new AccessRule();
        }
        $roles = $this->reversedRoleHierarchy->getParentRoles([Roles::TEAM_MEMBER]);
        $routes = $this->accessControlService->getRoutes();
        $form = $this->createForm(RoutingAccessRuleType::class, $accessRule, [
            'routes' => $routes,
            'roles' => $roles,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accessRule->setIsRoutingRule(true);
            $this->accessControlService->createRule($accessRule);

            if ($isCreate) {
                $this->addFlash('success', 'Access rule created');
            } else {
                $this->addFlash('success', 'Access rule edited');
            }

            return $this->redirectToRoute('access_rules_show');
        }

        return $this->render('admin/access_rule/create.html.twig', [
            'form' => $form->createView(),
            'accessRule' => $accessRule,
            'isCreate' => $isCreate,
        ]);
    }

    public function copyAccessRule(Request $request, AccessRule $rule): Response
    {
        $clone = clone $rule;
        if ($rule->isRoutingRule()) {
            return $this->createRoutingRule($request, $clone);
        }

        return $this->createRule($request, $clone);
    }

    public function delete(AccessRule $accessRule): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($accessRule);
        $em->flush();

        $this->addFlash('success', $accessRule->getName() . ' removed');

        return $this->redirectToRoute('access_rules_show');
    }
}
