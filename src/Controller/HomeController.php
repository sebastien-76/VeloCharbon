<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\BlogRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BlogRepository $blogRepository): Response
    {
        $latestBlogs = $blogRepository->findBy([], ['createdAt' => 'DESC'], 8);

        return $this->render('home/index.html.twig', [
            'latestBlogs' => $latestBlogs,
        ]);
    }
}
