<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Journey;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;
use App\Entity\BlogComment;
use Symfony\UX\Map\Polyline;
use App\Form\BlogCommentType;
use App\Repository\BlogRepository;
use App\Repository\ForumRepository;
use App\Repository\JourneyRepository;
use App\Repository\CarouselRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BlogRepository $blogRepository, CarouselRepository $carouselRepository): Response
    {
        $latestBlogs = $blogRepository->findBy([], ['createdAt' => 'DESC'], 8);
        $carouselImages = $carouselRepository->findAll();

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

    #[Route('/blog', name: 'app_blog_index', methods: ['GET'])]
    public function indexBlog(BlogRepository $blogRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    #[Route('/blog/{id}', name: 'app_blog_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function blogShow(Blog $blog): Response
    {
        $blogComments = $blog->getBlogComment();

        return $this->render('/blog/show.html.twig', [
            'blog' => $blog,
            'blogComments' => $blogComments
        ]);
    }

    #[route('/blog/comment/add/{blogId}', name: 'app_blog_comment_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, int $blogId, BlogRepository $blogRepository, TokenInterface $token): Response
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

    #[Route('/blog/comment/{id}/edit', name: 'app_blog_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blog = $blogComment->getBlog();
        $blogId = $blog->getId();

        $user= $blogComment->getUser();

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

    #[Route('/blog/comment/{id}', name: 'app_blog_comment_delete', methods: ['POST'])]
    public function delete(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blogId = $blogComment->getBlog()->getId();
        if ($this->isCsrfTokenValid('delete' . $blogComment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blogComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_show', ['id' => $blogId], Response::HTTP_SEE_OTHER);
    }


    #[Route('/journey', name: 'app_journey_index', methods: ['GET'])]
    public function indexJourney(JourneyRepository $journeyRepository): Response
    {
        return $this->render('journey/index.html.twig', [
            'journeys' => $journeyRepository->findAll(),
        ]);
    }

    #[Route('/journey/{id}', name: 'app_journey_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Journey $journey): Response
    {

        $nomFichier = $journey->getGpxName();
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

}
