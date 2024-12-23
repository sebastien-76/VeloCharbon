<?php

namespace App\Controller\Administration;

use App\Entity\BlogComment;
use App\Form\BlogCommentType;
use App\Repository\BlogCommentRepository;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/blog/comment')]
final class BlogCommentController extends AbstractController
{
    #[Route(name: 'app_blog_comment_index', methods: ['GET'])]
    public function index(BlogCommentRepository $blogCommentRepository): Response
    {
        return $this->render('blog_comment/index.html.twig', [
            'blog_comments' => $blogCommentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_blog_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blogComment = new BlogComment();
        $form = $this->createForm(BlogCommentType::class, $blogComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blogComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog_comment/new.html.twig', [
            'blog_comment' => $blogComment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_comment_show', methods: ['GET'])]
    public function show(BlogComment $blogComment): Response
    {
        return $this->render('blog_comment/show.html.twig', [
            'blog_comment' => $blogComment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blogId = $blogComment->getBlog()->getId();
        $form = $this->createForm(BlogCommentType::class, $blogComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog_comment/edit.html.twig', [
            'blog_comment' => $blogComment,
            'form' => $form,
            'blogId' => $blogId,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_comment_delete', methods: ['POST'])]
    public function delete(Request $request, BlogComment $blogComment, EntityManagerInterface $entityManager): Response
    {
        $blogId = $blogComment->getBlog()->getId();
        if ($this->isCsrfTokenValid('delete' . $blogComment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blogComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_show', ['id' => $blogId], Response::HTTP_SEE_OTHER);
    }

    #[route('/add/{blogId}', name: 'app_blog_comment_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, int $blogId, BlogRepository $blogRepository, UserRepository $userRepository): Response
    {
        $blog = $blogRepository->find($blogId);
        $blogComment = new BlogComment();
        $form = $this->createForm(BlogCommentType::class, $blogComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
}
