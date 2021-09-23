<?php
namespace App\Controller;

use App\Entity\Feedback;
use App\Form\Type\FeedbackType;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SlackMessenger;

class FeedbackController extends BaseController
{

    /**
     * @var Paginator
     */
    private $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    //shows form for submitting a new feedback
    public function index(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();

        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        $returnUri = $request->getUri();
        if ($request->headers->get('referer')) {
            $returnUri = $request->headers->get('referer');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            //Stores the submitted feedback
            $em = $this->getDoctrine()->getManager();
            $feedback = $form->getData();
            $feedback->setUser($user);
            $em->persist($feedback);
            $em->flush();

            //Notifies on slack (NotificationChannel)
            $messenger = $this->container->get(SlackMessenger::class);
            $messenger->notify($feedback->getSlackMessageBody());

            $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");
            
            return $this->redirect($returnUri); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)// Will also r
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback'
        ));
    }
    //Shows a specific feedback
    public function show(Request $request, Feedback $feedback)
    {
        return $this->render('feedback_admin/feedback_admin_show.html.twig', array(
            'feedback' => $feedback,
            'title' => $feedback->getTitle(),
        ));
    }

    //Lists all feedbacks
    public function showAll(Request $request)
    {
        $paginator  = $this->paginator;

        $repository = $this->getDoctrine()->getRepository(Feedback::class);

        //Gets all feedbacks sorted by created_at
        $feedbacks = $repository->findAllSortByNewest();

        $pagination = $paginator->paginate(
            $feedbacks,
            $request->query->get('page', 1),
            15
        );

        return $this->render('feedback_admin/feedback_admin_list.html.twig', array(
            'feedbacks' => $feedbacks,
            'pagination' => $pagination,
            'title' => 'Alle tilbakemeldinger'
        ));
    }
    public function delete(Feedback $feedback)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($feedback);
        $em->flush();

        $this->addFlash("success", "\"". $feedback->getTitle()."\" ble slettet");

        return $this->redirect($this->generateUrl('feedback_admin_list'));
    }
}
