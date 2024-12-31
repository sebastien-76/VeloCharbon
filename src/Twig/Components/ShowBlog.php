<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\BlogComment;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class ShowBlog extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    public function __construct(private BlogRepository $blogRepository, private UserRepository $userRepository)
    {
    }

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?string $title = null;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?string $content = null;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?User $user = null;

    #[LiveProp(writable: true)]
    #[NotBlank]
    public ?BlogComment $blogComment = null;

    #[ExposeInTemplate]
    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
