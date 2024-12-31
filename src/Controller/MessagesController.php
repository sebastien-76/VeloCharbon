<?php

namespace App\Controller;

use App\Form\ForumCommentType;
use App\Entity\ForumComment;
use App\Repository\ForumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route(path:"/messages")]

class MessagesController extends AbstractController
{

    #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository): Response
    {
        $forums = $forumRepository->findAll();
        return $this->render('messages/index.html.twig', [
            'forums' => $forums,
        ]);
    }

    #[Route('/forum/{forumId}', name: 'app_forum_show', methods: ['GET'], requirements:['id' => Requirement::DIGITS])]
    public function show(int $forumId, ForumRepository $forumRepository): Response
    {
        $forum = $forumRepository->find($forumId);
        $forums = $forumRepository->findAll();
        $comments = $forum->getForumComment();
        return $this->render('messages/show.html.twig', [
            'forum' => $forum,
            'forums'=> $forums,
            'comments'=> $comments,
            'forumId'=> $forumId
        ]);
    }

    #[route('/add/{forumId}', name: 'app_forum_comment_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, int $forumId, ForumRepository $forumRepository, TokenInterface $token, UserRepository $userRepository): Response
    {

        $forum = $forumRepository->find($forumId);
        $user = $token->getUser();
        $forumComment = new forumComment();
        $form = $this->createForm(ForumCommentType::class, $forumComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumComment->setUser($user);
            $forumComment->setForum($forum);
            $entityManager->persist($forumComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_show', ['forumId'=> $forumId], Response::HTTP_SEE_OTHER); 
        }

        return $this->render('/messages/new.html.twig', [
            'forumId' => $forumId,
            'forumComment' => $forumComment,
            'form' => $form,
        ]);
    }

    #[Route('/forumComment/{forumCommentId}/edit', name: 'app_forum_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumComment $forumComment, EntityManagerInterface $entityManager): Response
    {
        $forumId = $forumComment->getForum()->getId();
        $form = $this->createForm(ForumCommentType::class, $forumComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/forum_comment/edit.html.twig', [
            'forumComment' => $forumComment,
            'form' => $form,
            'forumId' => $forumId,
        ]);
    }
}