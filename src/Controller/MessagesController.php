<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Entity\ForumComment;
use App\Form\ForumCommentType;
use App\Repository\UserRepository;
use App\Repository\ForumRepository;
use App\Repository\CategoryRepository;
use Proxies\__CG__\App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[Route(path:"/messages")]

class MessagesController extends AbstractController
{

    #[Route('/forum', name: 'app_forum_index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository, CategoryRepository $categoryRepository, EntityManagerInterface $em, UserRepository $userRepository): Response 
    {
        $categories = $categoryRepository->findAll();
        $forums = $forumRepository->findAll();
        return $this->render('messages/index.html.twig', [
            'forums' => $forums,
            'categories'=> $categories,
        ]);
    }

    #[Route('/forum/new', name: 'app_forum_new', methods: ['GET', 'POST'])]
    public function newForum(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, TokenInterface $token): Response
    {
        $forum = new Forum();
        $user = $token->getUser();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setUser($user);
            $entityManager->persist($forum);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('messages/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[Route('/forum/{forumId}', name: 'app_forum_show', methods: ['GET'], requirements:['id' => Requirement::DIGITS])]
    public function show(int $forumId, ForumRepository $forumRepository, CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $forum = $forumRepository->find($forumId);
        $forums = $forumRepository->findAll();
        $comments = $forum->getForumComment();
        return $this->render('messages/show.html.twig', [
            'forum' => $forum,
            'forums'=> $forums,
            'comments'=> $comments,
            'forumId'=> $forumId,
            'categories'=> $categories,
        ]);
    }

    #[route('/add/{forumId}', name: 'app_forum_comment_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, int $forumId, ForumRepository $forumRepository, TokenInterface $token): Response
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

        return $this->render('/messages/forum_comment_new.html.twig', [
            'forumId' => $forumId,
            'forumComment' => $forumComment,
            'form' => $form,
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_forum_comment_edit', methods: ['GET', 'POST'])]
    public function commentEdit(Request $request, ForumComment $forumComment, EntityManagerInterface $entityManager): Response
    {
        $forum = $forumComment->getForum();
        $forumId = $forum->getId();
        $user = $forumComment->getUser();
        $form = $this->createForm(ForumCommentType::class, $forumComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forumComment->setUser($user);
            $forumComment->setForum($forum);
            $entityManager->persist($forumComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_show', ['forumId' => $forumId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/messages/forum_comment_edit.html.twig', [
            'forumComment' => $forumComment,
            'form' => $form,
            'forumId' => $forumId,
        ]);
    }
        #[Route('/forum/{id}/edit', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    public function forumEdit(Request $request, Forum $forum, EntityManagerInterface $entityManager): Response
    {
        $forumId = $forum->getId();
        $user = $forum->getUser();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setUser($user);
            $entityManager->persist($forum);
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_show', ['forumId' => $forumId], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/messages/edit.html.twig', [
            'form' => $form,
            'forumId' => $forumId,
        ]);
    
    }

    #[Route('/forumComment/{id}', name: 'app_forum_comment_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function deleteForumComment(Request $request, ForumComment $forumComment, EntityManagerInterface $entityManager): Response
    {
        $forumId = $forumComment->getForum()->getId();
        if ($this->isCsrfTokenValid('delete' . $forumComment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($forumComment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_show', ['forumId'=> $forumId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/forum/{id}', name: 'app_forum_delete', methods: ['POST'], requirements: ['id' => Requirement::DIGITS])]
    public function deleteForum(Request $request, Forum $forum, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $forum->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($forum);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}