<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BlogRepository;
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
}

