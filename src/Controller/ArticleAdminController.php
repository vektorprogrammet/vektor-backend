<?php

namespace App\Controller;

use App\Service\FileUploader;
use App\Service\LogService;
use App\Service\SlugMaker;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Article;
use App\Form\Type\ArticleType;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArticleAdminController is the controller responsible for the administrative article s,
 * such as creating and deleting articles.
 */
class ArticleAdminController extends BaseController
{
    // Number of articles shown per page on the admin page
    const NUM_ARTICLES = 10;

    /**
     * @var PaginatorInterface
     */
    private $paginatorInterface;
    /**
     * @var LogService
     */
    private $logService;
    /**
     * @var SlugMaker
     */
    private $slugMaker;
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(PaginatorInterface $paginatorInterface, LogService $logService,
                                SlugMaker $slugMaker, FileUploader $fileUploader) {
        $this->paginatorInterface = $paginatorInterface;
        $this->logService = $logService;
        $this->slugMaker = $slugMaker;
        $this->fileUploader = $fileUploader;
    }


    /**
     * Shows the main page of the article administration.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function show(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository(Article::class)->findAllArticles();

        // Uses the knp_paginator bundle to separate the articles into pages.
        $paginator  = $this->paginatorInterface;
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article_admin/index.html.twig', array(
            'pagination' => $pagination,
            'articles' => $articles->getQuery()->getResult()
        ));
    }

    /**
     * @Route("/kontrollpanel/artikkel/kladd/{slug}", name="article_show_draft")
     * @param Article $article
     *
     * @return Response
     */
    public function showDraft(Article $article)
    {
        return $this->render('article/show.html.twig', array('article' => $article, 'isDraft' => true));
    }

    /**
     * Shows and handles the submission of the article creation form.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $article       = new Article();
        $form          = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->slugMaker->setSlugFor($article);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Set the author to the currently logged in user
            $article->setAuthor($this->getUser());

            $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
            $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
            if (!$imageSmall || !$imageLarge) {
                return new JsonResponse("Error", 400);
            }

            $article->setImageSmall($imageSmall);
            $article->setImageLarge($imageLarge);

            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Artikkelen har blitt publisert.'
            );

            $this->logService->info("A new article \"{$article->getTitle()}\" by {$article->getAuthor()} has been published");

            return new JsonResponse("ok");
        } elseif ($form->isSubmitted()) {
            return new JsonResponse("Error", 400);
        }

        return $this->render('article_admin/form.html.twig', array(
            'article'       => $article,
            'title'         => 'Legg til en ny artikkel',
            'form'          => $form->createView(),
        ));
    }

    /**
     * Shows and handles the submission of the article edit form.
     * Uses the same form type as article creation.
     *
     * @param Request $request
     * @param Article $article
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
            if ($imageSmall) {
                $article->setImageSmall($imageSmall);
            }
            $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
            if ($imageLarge) {
                $article->setImageLarge($imageLarge);
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Endringene har blitt publisert.'
            );

            $this->logService->info("The article \"{$article->getTitle()}\" was edited by {$this->getUser()}");

            return new JsonResponse("ok");
        } elseif ($form->isSubmitted()) {
            return new JsonResponse("Error", 400);
        }

        return $this->render('article_admin/form.html.twig', array(
            'article' => $article,
            'title'   => 'Endre artikkel',
            'form'    => $form->createView(),
        ));
    }

    /**
     * Set/unset the sticky boolean on the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @param Article $article
     *
     * @return JsonResponse
     */
    public function sticky(Article $article)
    {
        try {
            if ($article->getSticky()) {
                $article->setSticky(false);
                $response['sticky'] = false;
            } else {
                $article->setSticky(true);
                $response['sticky'] = true;
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $response['success'] = true;
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'code'    => $e->getCode(),
                'cause'   => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @param Article $article
     *
     * @return RedirectResponse
     */
    public function delete(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $this->addFlash("success", "Artikkelen ble slettet");

        return $this->redirectToRoute('articleadmin_show');
    }
}
