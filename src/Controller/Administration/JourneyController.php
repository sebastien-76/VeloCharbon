<?php

namespace App\Controller\Administration;

use App\Entity\Journey;
use Symfony\UX\Map\Map;
use App\Form\JourneyType;
use Symfony\UX\Map\Point;
use App\Repository\JourneyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\Map\Polyline;

#[Route('/admin/journey')]
final class JourneyController extends AbstractController
{
    #[Route(name: 'app_admin_journey_index', methods: ['GET'])]
    public function index(JourneyRepository $journeyRepository): Response
    {
        $journeys = $journeyRepository->findAll();
        return $this->render('/Administration/journey/index.html.twig', [
            'journeys' => $journeys,
        ]);
    }
    
    #[Route('/new', name: 'app_admin_journey_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $map = (new Map())
            ->center(new Point(46.903354, 1.888334))
            ->zoom(6)
            ->fitBoundsToMarkers();
        $journey = new Journey();
        $form = $this->createForm(JourneyType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($journey);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_journey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/Administration/journey/new.html.twig', [
            'journey' => $journey,
            'form' => $form,
            'map' => $map
        ]);
    }

    #[Route('/{id}', name: 'app_admin_journey_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
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
        /*         dd($map); */
        return $this->render('/Administration/journey/show.html.twig', [
            'journey' => $journey,
            'map' => $map
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_journey_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JourneyType::class, $journey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_journey_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/Administration/journey/edit.html.twig', [
            'journey' => $journey,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_journey_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$journey->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($journey);
        //     $entityManager->flush();
        // }
        $entityManager->remove($journey);
        $entityManager->flush();

        return $this->redirectToRoute('/Administration/app_admin_journey_index', [], Response::HTTP_SEE_OTHER);
    }
}
