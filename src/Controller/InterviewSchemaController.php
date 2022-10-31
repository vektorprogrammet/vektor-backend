<?php

namespace App\Controller;

use App\Role\Roles;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\InterviewSchema;
use App\Form\Type\InterviewSchemaType;
use Symfony\Component\HttpFoundation\Response;

/**
 * InterviewController is the controller responsible for interview s,
 * such as showing, assigning and conducting interviews.
 */
class InterviewSchemaController extends BaseController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Shows and handles the submission of the create interview schema form.
     * Uses the same form as the edit .
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createSchema(Request $request)
    {
        $schema = new InterviewSchema();

        return $this->editSchema($request, $schema);
    }

    /**
     * Shows and handles the submission of the edit interview schema form.
     * Uses the same form as the create .
     *
     * @param Request         $request
     * @param InterviewSchema $schema
     *
     * @return RedirectResponse|Response
     */
    public function editSchema(Request $request, InterviewSchema $schema)
    {
        $form = $this->createForm(InterviewSchemaType::class, $schema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($schema);
            $em->flush();
            return $this->redirect($this->generateUrl('interview_schema'));
        }

        return $this->render('interview/schema.html.twig', array(
            'form' => $form->createView(),
            'schema' => $schema,
            'isCreate' => !$schema->getId()
        ));
    }

    /**
     * Shows the interview schemas page.
     *
     * @return Response
     */
    public function showSchemas(): Response
    {
        $schemas = $this->doctrine->getRepository(InterviewSchema::class)->findAll();

        return $this->render('interview/schemas.html.twig', array('schemas' => $schemas));
    }

    /**
     * Deletes the given interview schema.
     * This method is intended to be called by an Ajax request.
     *
     * @param InterviewSchema $schema
     *
     * @return JsonResponse
     */
    public function deleteSchema(InterviewSchema $schema): JsonResponse
    {
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
        } catch (Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }
}
