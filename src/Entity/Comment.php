<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $commentTitle = null;

    #[ORM\Column]
    private ?\DateTime $publicationDate = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'comment_on')]
    private ?Post $realtedPost = null;

    /**
     * @var Collection<int, SubComment>
     */
    #[ORM\OneToMany(targetEntity: SubComment::class, mappedBy: 'comment')]
    private Collection $answerTo;

    #[ORM\ManyToOne(inversedBy: 'respond')]
    private ?User $commentUser = null;

    public function __construct()
    {
        $this->answerTo = new ArrayCollection();
        $this->setPublicationDate(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentTitle(): ?string
    {
        return $this->commentTitle;
    }

    public function setCommentTitle(string $commentTitle): static
    {
        $this->commentTitle = $commentTitle;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRealtedPost(): ?Post
    {
        return $this->realtedPost;
    }

    public function setRealtedPost(?Post $realtedPost): static
    {
        $this->realtedPost = $realtedPost;

        return $this;
    }

    /**
     * @return Collection<int, SubComment>
     */
    public function getAnswerTo(): Collection
    {
        return $this->answerTo;
    }

    public function addAnswerTo(SubComment $answerTo): static
    {
        if (!$this->answerTo->contains($answerTo)) {
            $this->answerTo->add($answerTo);
            $answerTo->setComment($this);
        }

        return $this;
    }

    public function removeAnswerTo(SubComment $answerTo): static
    {
        if ($this->answerTo->removeElement($answerTo)) {
            // set the owning side to null (unless already changed)
            if ($answerTo->getComment() === $this) {
                $answerTo->setComment(null);
            }
        }

        return $this;
    }

    public function getCommentUser(): ?User
    {
        return $this->commentUser;
    }

    public function setCommentUser(?User $commentUser): static
    {
        $this->commentUser = $commentUser;

        return $this;
    }

    public function __toString() {
        return "" . $this->commentTitle . "";
    }
}
