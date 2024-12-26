<?php

namespace App\Controller;

use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;
use Symfony\UX\Map\Polyline;
use App\Repository\BlogRepository;
use App\Repository\ForumRepository;
use App\Repository\JourneyRepository;
use App\Repository\CarouselRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    public function indexForum(ForumRepository $forumRepository): Response
    {
        return $this->render('/Administration/forum/index.html.twig', [
            'forums' => $forumRepository->findAll(),
        ]);
    }
}
