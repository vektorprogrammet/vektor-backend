<?php

namespace App\Controller;

use App\Entity\InterviewSchema;
use App\Form\Type\InterviewSchemaType;
use App\Role\Roles;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * InterviewController is the controller responsible for interview s,
 * such as showing, assigning and conducting interviews.
 */
class InterviewSchemaController extends BaseController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * Shows and handles the submission of the create interview schema form.
     * Uses the same form as the edit .
     */
    public function createSchema(Request $request): RedirectResponse|Response
    {
        $schema = new InterviewSchema();

        return $this->editSchema($request, $schema);
    }

    /**
     * Shows and handles the submission of the edit interview schema form.
     * Uses the same form as the create .
     */
    public function editSchema(Request $request, InterviewSchema $schema): RedirectResponse|Response
    {
        $form = $this->createForm(InterviewSchemaType::class, $schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($schema);
            $em->flush();

            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', [
            'form' => $form->createView(),
            'schema' => $schema,
            'isCreate' => !$schema->getId(),
        ]);
    }

    /**
     * Shows the interview schemas page.
     */
    public function showSchemas(): Response
    {
        $schemas = $this->doctrine->getRepository(InterviewSchema::class)->findAll();

        return $this->render('interview/schemas.html.twig', ['schemas' => $schemas]);
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     */
    public function deleteSchema(InterviewSchema $schema): JsonResponse
    {
        $response = [];
        try {
            if ($this->isGranted(Roles::TEAM_LEADER)) {
                $em = $this->doctrine->getManager();
                $em->remove($schema);
                $em->flush();

                $response['success'] = true;
            } else {
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelig rettigheter';
            }
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }
}
