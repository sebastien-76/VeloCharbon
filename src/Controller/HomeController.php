<?php

namespace App\Controller;

use App\Entity\Journey;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;
use Symfony\UX\Map\Polyline;
use App\Repository\BlogRepository;
use App\Repository\ForumRepository;
use App\Repository\JourneyRepository;
use App\Repository\CarouselRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
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

    #[Route('/journey', name: 'app_journey_index', methods: ['GET'])]
    public function indexJourney(JourneyRepository $journeyRepository): Response
    {
        return $this->render('journey/index.html.twig', [
            'journeys' => $journeyRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_journey_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
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

    #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    public function indexForum(ForumRepository $forumRepository): Response
    {
        return $this->render('/Administration/forum/index.html.twig', [
            'forums' => $forumRepository->findAll(),
        ]);
    }
}
