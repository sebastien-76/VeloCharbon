<?php

namespace App\Controller\Administration;

use App\Repository\JourneyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Point;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(JourneyRepository $journeyRepository): Response
    {


        {
            $map = (new Map())
                ->center(new Point(46.903354, 1.888334))
                ->zoom(6)
                ->fitBoundsToMarkers()
            ;

           return $this->render('Administration/map/_map.html.twig', [
                'map' => $map,
            ]);
        }
    }
}
