<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $postTitle = null;

    #[ORM\Column]
    private ?\DateTime $publicationDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textuelContent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $visuelContent = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'realtedPost')]
    private Collection $comment_on;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    private Collection $associatedTo;

    #[ORM\ManyToOne(inversedBy: 'posting')]
    private ?User $userOfPost = null;

    public function __construct()
    {
        $this->comment_on = new ArrayCollection();
        $this->associatedTo = new ArrayCollection();
        $this->setPublicationDate(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): static
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    public function getPublicationDate(): ?\DateTime
    {
        return $this->publicationDate;
    }

    public function getPublicationDateFr(): ?string {
        return $this->publicationDate->format("d-m-Y");
    }

    public function setPublicationDate(\DateTime $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getTextuelContent(): ?string
    {
        return $this->textuelContent;
    }

    public function setTextuelContent(string $textuelContent): static
    {
        $this->textuelContent = $textuelContent;

        return $this;
    }

    public function getVisuelContent(): ?string
    {
        return $this->visuelContent;
    }

    public function setVisuelContent(?string $visuelContent): static
    {
        $this->visuelContent = $visuelContent;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentOn(): Collection
    {
        return $this->comment_on;
    }

    public function addCommentOn(Comment $commentOn): static
    {
        if (!$this->comment_on->contains($commentOn)) {
            $this->comment_on->add($commentOn);
            $commentOn->setRealtedPost($this);
        }

        return $this;
    }

    public function removeCommentOn(Comment $commentOn): static
    {
        if ($this->comment_on->removeElement($commentOn)) {
            // set the owning side to null (unless already changed)
            if ($commentOn->getRealtedPost() === $this) {
                $commentOn->setRealtedPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getAssociatedTo(): Collection
    {
        return $this->associatedTo;
    }

    public function addAssociatedTo(Tag $associatedTo): static
    {
        if (!$this->associatedTo->contains($associatedTo)) {
            $this->associatedTo->add($associatedTo);
        }

        return $this;
    }

    public function removeAssociatedTo(Tag $associatedTo): static
    {
        $this->associatedTo->removeElement($associatedTo);

        return $this;
    }

    public function getUserOfPost(): ?User
    {
        return $this->userOfPost;
    }

    public function setUserOfPost(?User $userOfPost): static
    {
        $this->userOfPost = $userOfPost;

        return $this;
    }

    public function __toString() {
        return "" . $this->postTitle . "";
    }
}
