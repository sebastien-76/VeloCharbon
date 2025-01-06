<?php

namespace App\Controller\Administration;

use App\Entity\ForumComment;
use App\Form\ForumCommentType;
use App\Repository\ForumCommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/forum/comment')]
final class ForumCommentController extends AbstractController
{
    #[Route(name: 'app_admin_forum_comment_index', methods: ['GET'])]
    public function index(ForumCommentRepository $forumCommentRepository): Response
    {
        return $this->render('/Administration/forum_comment/index.html.twig', [
            'forum_comments' => $forumCommentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_forum_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forumComment = new ForumComment();
        $form = $this->createForm(ForumCommentType::class, $forumComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forumComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_forum_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/Administration/forum_comment/new.html.twig', [
            'forum_comment' => $forumComment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_forum_comment_show', methods: ['GET'])]
    public function show(ForumComment $forumComment): Response
    {
        return $this->render('/Administration/forum_comment/show.html.twig', [
            'forum_comment' => $forumComment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_forum_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumComment $forumComment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumCommentType::class, $forumComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_forum_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/Administration/forum_comment/edit.html.twig', [
            'forum_comment' => $forumComment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_forum_comment_delete', methods: ['POST'])]
    public function delete(Request $request, ForumComment $forumComment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forumComment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($forumComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_forum_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
