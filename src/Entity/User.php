<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    /**

     * @var Collection<int, Forum>
     */
    #[ORM\OneToMany(targetEntity: Forum::class, mappedBy: 'user')]
    private Collection $forums;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->blogs = new ArrayCollection();
        $this->blogComment = new ArrayCollection();
        $this->forumComment = new ArrayCollection();
    }

    /**
     * @var Collection<int, Blog>
     */
    #[ORM\OneToMany(targetEntity: Blog::class, mappedBy: 'user')]
    private Collection $blogs;

    /**
     * @var Collection<int, BlogComment>
     */
    #[ORM\OneToMany(targetEntity: BlogComment::class, mappedBy: 'user')]
    private Collection $blogComment;

    /**
     * @var Collection<int, ForumComment>
     */
    #[ORM\OneToMany(targetEntity: ForumComment::class, mappedBy: 'user')]
    private Collection $forumComment;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    /**

     * @return Collection<int, Forum>
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): static
    {
        if (!$this->forums->contains($forum)) {
            $this->forums->add($forum);
            $forum->setUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Blog>
     */
    public function getBlogs(): Collection
    {
        return $this->blogs;
    }

    public function addBlog(Blog $blog): static
    {
        if (!$this->blogs->contains($blog)) {
            $this->blogs->add($blog);
            $blog->setUser($this);
        }

        return $this;
    }


    public function removeForum(Forum $forum): static
    {
        if ($this->forums->removeElement($forum)) {
            // set the owning side to null (unless already changed)
            if ($forum->getUser() === $this) {
                $forum->setUser(null);
            }
        }

        return $this;
    }

    public function removeBlog(Blog $blog): static
    {
        if ($this->blogs->removeElement($blog)) {
            // set the owning side to null (unless already changed)
            if ($blog->getUser() === $this) {
                $blog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlogComment>
     */
    public function getBlogComment(): Collection
    {
        return $this->blogComment;
    }

    public function addBlogComment(BlogComment $blogComment): static
    {
        if (!$this->blogComment->contains($blogComment)) {
            $this->blogComment->add($blogComment);
            $blogComment->setUser($this);
        }

        return $this;
    }

    public function removeBlogComment(BlogComment $blogComment): static
    {
        if ($this->blogComment->removeElement($blogComment)) {
            // set the owning side to null (unless already changed)
            if ($blogComment->getUser() === $this) {
                $blogComment->setUser(null);
            }
        }

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
            $forumComment->setUser($this);
        }

        return $this;
    }

    public function removeForumComment(ForumComment $forumComment): static
    {
        if ($this->forumComment->removeElement($forumComment)) {
            // set the owning side to null (unless already changed)
            if ($forumComment->getUser() === $this) {
                $forumComment->setUser(null);
            }
        }

        return $this;
    }
}
