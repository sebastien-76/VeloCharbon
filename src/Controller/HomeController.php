<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Journey;
use Symfony\UX\Map\Map;
use App\Form\JourneyType;
use Symfony\UX\Map\Point;
use App\Entity\BlogComment;
use Symfony\UX\Map\Polyline;
use App\Form\BlogCommentType;
use App\Repository\BlogRepository;
use App\Repository\JourneyRepository;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class HomeController extends AbstractController
{
    //Route de la page d'accueil
    #[Route('/', name: 'app_home')]
    public function index(BlogRepository $blogRepository, CarouselRepository $carouselRepository): Response
    {
        //Récupération pour affichage des 8 dernières actualités
        $latestBlogs = $blogRepository->findBy([], ['createdAt' => 'DESC'], 8);
        //Récupération des images du carousel
        $carouselImages = $carouselRepository->findAll();
        //Création de la carte du trajet global
        $gpx = simplexml_load_file("../public/gpxFiles/EuroVelo_5_Via_Romea.gpx");
        $trseg = $gpx->trk->trkseg;
        $map = (new Map())
            ->center(new Point(50.566669, 2.48333))
            ->zoom(9);
        foreach ($trseg->trkpt as $trkpt) {
            $lat = (float) $trkpt['lat'];
            $lon = (float) $trkpt['lon'];
            $points[] = new Point($lat, $lon);
        }
        $map->addPolyLine(
            new Polyline(
                $points
            )
        );

        return $this->render('home/index.html.twig', [
            'latestBlogs' => $latestBlogs,
            'carouselImages' => $carouselImages,
            'map' => $map
        ]);
    }

    //Route de la page publique des index des actualités
    #[Route('/blog', name: 'app_blog_index', methods: ['GET'])]
    public function blogIndex(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    //Route de la page publique d'une actualité
    #[Route('/blog/{id}', name: 'app_blog_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function blogShow(Blog $blog): Response
    {
        $blogComments = $blog->getBlogComment();

        return $this->render('/blog/show.html.twig', [
            'blog' => $blog,
            'blogComments' => $blogComments
        ]);
    }

    //Route de la page publique d'ajout d'un commentaire sur une actualité
    #[route('/blog/comment/add/{blogId}', name: 'app_blog_comment_add', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function commentAdd(Request $request, EntityManagerInterface $entityManager, int $blogId, BlogRepository $blogRepository, TokenInterface $token): Response
    {
        $blog = $blogRepository->find($blogId);
        $user = $token->getUser();
        $blogComment = new BlogComment();
        $form = $this->createForm(BlogCommentType::class, $blogComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogComment->setUser($user);
            $blogComment->setBlog($blog);
            $entityManager->persist($blogComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_show', [
                'id' => $blogId,
                'blogComment' => $blogComment,
                'form' => $form,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog_comment/new.html.twig', [
            'blogId' => $blogId,
            'blogComment' => $blogComment,
            'form' => $form,
        ]);
    }

    //Route de la page publique d'édition d'un commentaire d'une actualité
    #[Route('/blog/comment/{id}/edit', name: 'app_blog_comment_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function commentEdit(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blog = $blogComment->getBlog();
        $blogId = $blog->getId();

        $user = $blogComment->getUser();

        $form = $this->createForm(BlogCommentType::class, $blogComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blogComment->setBlog($blog);
            $blogComment->setUser($user);
            $entityManager->persist($blogComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_show', ['id' => $blogId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog_comment/edit.html.twig', [
            'blog_comment' => $blogComment,
            'form' => $form,
            'blogId' => $blogId,
        ]);
    }

    //Route de la page publique de suppression d'un commentaire d'une actualité
    #[Route('/blog/comment/{id}', name: 'app_blog_comment_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function commentDelete(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blogId = $blogComment->getBlog()->getId();
        if ($this->isCsrfTokenValid('delete' . $blogComment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blogComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_show', ['id' => $blogId], Response::HTTP_SEE_OTHER);
    }

    //Route de la page publique de l'index des trajets
    #[Route('/journey', name: 'app_journey_index', methods: ['GET'])]
    public function journeyIndex(JourneyRepository $journeyRepository): Response
    {
        return $this->render('journey/index.html.twig', [
            'journeys' => $journeyRepository->findAll(),
        ]);
    }

    //Route de la page publique d'un trajet
    #[Route('/journey/{id}', name: 'app_journey_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function journeyShow(Journey $journey): Response
    {
        //Récupération du nom du gpx associé au trajet
        $nomFichier = $journey->getGpxName();

        //Récupération des coordonnées du trajet et création de ma carte associée
        if ($nomFichier) {
            $gpx = simplexml_load_file("../public/gpxFiles/$nomFichier");
            $trseg = $gpx->trk->trkseg;
            $centerPoint = $trseg->trkpt[0];
            $centerLat = (float) $centerPoint['lat'];
            $centerLon = (float) $centerPoint['lon'];
            $map = (new Map())
                ->center(new Point($centerLat, $centerLon))
                ->zoom(10);
            foreach ($trseg->trkpt as $trkpt) {
                $lat = (float) $trkpt['lat'];
                $lon = (float) $trkpt['lon'];

                $points[] = new Point($lat, $lon);
            }
            $map->addPolyLine(
                new Polyline(
                    $points
                )
            );
        } else {
            $map = null;
        }

        return $this->render('journey/show.html.twig', [
            'journey' => $journey,
            'map' => $map
        ]);
    }

    //Route de la page publique de création d'un trajet
    #[Route('/journey/new', name: 'app_journey_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MOD')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $journey = new Journey();
        $form = $this->createForm(JourneyType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($journey);
            $entityManager->flush();

            return $this->redirectToRoute('app_journey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('journey/new.html.twig', [
            'journey' => $journey,
            'form' => $form,
        ]);
    }

    //Route de la page publique de modification d'un trajet
    #[Route('/journey/{id}/edit', name: 'app_journey_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('ROLE_MOD')]
    public function journeyEdit(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JourneyType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_journey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('journey/edit.html.twig', [
            'journey' => $journey,
            'form' => $form,
        ]);
    }

    //Route de la page publique de suppression d'un trajet    #[Route('/{id}', name: 'app_admin_journey_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    #[Route('/journey/{id}', name: 'app_journey_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('ROLE_MOD')]
    public function journeyDelete(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$journey->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($journey);
        //     $entityManager->flush();
        // }
        $entityManager->remove($journey);
        $entityManager->flush();

        return $this->redirectToRoute('app_journey_index', [], Response::HTTP_SEE_OTHER);
    }

    //Route de la page publique du profil
    #[Route('/compte/{id}', name: 'app_profile_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('PROFILE_ACCESS', subject: "user")]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    //Route de la page publique d'édition du profil
    #[Route('compte/{id}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted('PROFILE_ACCESS', subject: "user")]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
