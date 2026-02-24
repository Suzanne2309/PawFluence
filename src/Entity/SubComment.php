<?php

namespace App\Entity;

use App\Repository\SubCommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubCommentRepository::class)]
class SubComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $publicationDate = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $subCommentTitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'answerTo')]
    private ?Comment $comment = null;

    #[ORM\ManyToOne(inversedBy: 'debating')]
    private ?User $subCommentUser = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSubCommentTitle(): ?string
    {
        return $this->subCommentTitle;
    }

    public function setSubCommentTitle(?string $subCommentTitle): static
    {
        $this->subCommentTitle = $subCommentTitle;

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSubCommentUser(): ?User
    {
        return $this->subCommentUser;
    }

    public function setSubCommentUser(?User $subCommentUser): static
    {
        $this->subCommentUser = $subCommentUser;

        return $this;
    }

    public function __toString() {
        return "" . $this->subCommentTitle . "";
    }
}
