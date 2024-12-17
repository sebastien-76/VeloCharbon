<?php

namespace App\Entity;

use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
#[ORM\Entity(repositoryClass: ForumRepository::class)]
class Forum
{   
    use SoftDeleteableEntity;
    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'forum')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, ForumComment>
     */
    #[ORM\OneToMany(targetEntity: ForumComment::class, mappedBy: 'forum')]
    private Collection $forumComment;

    #[ORM\ManyToOne(inversedBy: 'forums')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->forumComment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ForumComment>
     */
    public function getForumComment(): Collection
    {
        return $this->forumComment;
    }

    public function addForumComment(ForumComment $forumComment): static
    {
        if (!$this->forumComment->contains($forumComment)) {
            $this->forumComment->add($forumComment);
            $forumComment->setForum($this);
        }

        return $this;
    }

    public function removeForumComment(ForumComment $forumComment): static
    {
        if ($this->forumComment->removeElement($forumComment)) {
            // set the owning side to null (unless already changed)
            if ($forumComment->getForum() === $this) {
                $forumComment->setForum(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
