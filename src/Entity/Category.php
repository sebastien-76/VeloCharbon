<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    /**
     * @var Collection<int, Forum>
     */
    #[ORM\OneToMany(targetEntity: Forum::class, mappedBy: 'category')]
    private Collection $forum;

    public function __construct()
    {
        $this->forum = new ArrayCollection();
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

    /**
     * @return Collection<int, Forum>
     */
    public function getForum(): Collection
    {
        return $this->forum;
    }

    public function addForum(Forum $forum): static
    {
        if (!$this->forum->contains($forum)) {
            $this->forum->add($forum);
            $forum->setCategory($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): static
    {
        if ($this->forum->removeElement($forum)) {
            // set the owning side to null (unless already changed)
            if ($forum->getCategory() === $this) {
                $forum->setCategory(null);
            }
        }

        return $this;
    }
}
