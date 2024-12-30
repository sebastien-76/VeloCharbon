<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use App\Repository\ForumRepository;
use App\Repository\JourneyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\CarouselRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BlogRepository $blogRepository, CarouselRepository $carouselRepository): Response
    {
        $latestBlogs = $blogRepository->findBy([], ['createdAt' => 'DESC'], 8);
        $carouselImages = $carouselRepository->findAll();

        return $this->render('home/index.html.twig', [
            'latestBlogs' => $latestBlogs,
            'carouselImages' => $carouselImages,
        ]);
    }

    #[Route('/blog', name: 'app_home_blog_index', methods: ['GET'])]
    public function indexBlog(BlogRepository $blogRepository): Response
    {
        return $this->render('/Administration/blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }    
   
    #[Route('/journey', name: 'app_home_journey_index', methods: ['GET'])]
    public function indexJourney(JourneyRepository $journeyRepository): Response
    {
        return $this->render('/Administration/journey/index.html.twig', [
            'journeys' => $journeyRepository->findAll(),
        ]);
    }

    // #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    // public function indexForum(ForumRepository $forumRepository): Response
    // {
    //     return $this->render('/Administration/forum/index.html.twig', [
    //         'forums' => $forumRepository->findAll(),
    //     ]);
    // }
}

