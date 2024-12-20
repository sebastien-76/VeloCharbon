<?php

namespace App\Controller\Administration;

use App\Entity\Journey;
use App\Form\JourneyType;
use App\Repository\JourneyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/journey')]
final class JourneyController extends AbstractController
{
    #[Route(name: 'app_journey_index', methods: ['GET'])]
    public function index(JourneyRepository $journeyRepository): Response
    {
        $journeys = $journeyRepository->findAll();
        return $this->render('journey/index.html.twig', [
            'journeys' => $journeys,
        ]);
    
    }
    #[Route('/new', name: 'app_journey_new', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_journey_show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Journey $journey): Response
    {
        return $this->render('journey/show.html.twig', [
            'journey' => $journey,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_journey_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
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

    #[Route('/{id}', name: 'app_journey_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Request $request, Journey $journey, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$journey->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($journey);
        //     $entityManager->flush();
        // }
            $entityManager->remove($journey);
            $entityManager->flush();

        return $this->redirectToRoute('app_journey_index', [], Response::HTTP_SEE_OTHER);
    }
}
