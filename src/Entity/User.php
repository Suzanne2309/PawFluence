<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
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

    #[ORM\Column(length: 80)]
    private ?string $pseudo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?\DateTime $inscriptionDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $_description = null;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'userOfPost')]
    private Collection $posting;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'users')]
    private Collection $tagFollow;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'commentUser')]
    private Collection $respond;

    /**
     * @var Collection<int, SubComment>
     */
    #[ORM\OneToMany(targetEntity: SubComment::class, mappedBy: 'subCommentUser')]
    private Collection $debating;

    /**
     * @var Collection<int, Network>
     */
    #[ORM\OneToMany(targetEntity: Network::class, mappedBy: 'networkUser')]
    private Collection $user_network;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->posting = new ArrayCollection();
        $this->tagFollow = new ArrayCollection();
        $this->respond = new ArrayCollection();
        $this->debating = new ArrayCollection();
        $this->user_network = new ArrayCollection();
        $this->setInscriptionDate(new DateTime('now'));
    }

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
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getInscriptionDate(): ?\DateTime
    {
        return $this->inscriptionDate;
    }

    public function setInscriptionDate(\DateTime $inscriptionDate): static
    {
        $this->inscriptionDate = $inscriptionDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->_description;
    }

    public function setDescription(?string $description): static
    {
        $this->_description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosting(): Collection
    {
        return $this->posting;
    }

    public function addPosting(Post $posting): static
    {
        if (!$this->posting->contains($posting)) {
            $this->posting->add($posting);
            $posting->setUserOfPost($this);
        }

        return $this;
    }

    public function removePosting(Post $posting): static
    {
        if ($this->posting->removeElement($posting)) {
            // set the owning side to null (unless already changed)
            if ($posting->getUserOfPost() === $this) {
                $posting->setUserOfPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTagFollow(): Collection
    {
        return $this->tagFollow;
    }

    public function addTagFollow(Tag $tagFollow): static
    {
        if (!$this->tagFollow->contains($tagFollow)) {
            $this->tagFollow->add($tagFollow);
        }

        return $this;
    }

    public function removeTagFollow(Tag $tagFollow): static
    {
        $this->tagFollow->removeElement($tagFollow);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getRespond(): Collection
    {
        return $this->respond;
    }

    public function addRespond(Comment $respond): static
    {
        if (!$this->respond->contains($respond)) {
            $this->respond->add($respond);
            $respond->setCommentUser($this);
        }

        return $this;
    }

    public function removeRespond(Comment $respond): static
    {
        if ($this->respond->removeElement($respond)) {
            // set the owning side to null (unless already changed)
            if ($respond->getCommentUser() === $this) {
                $respond->setCommentUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SubComment>
     */
    public function getDebating(): Collection
    {
        return $this->debating;
    }

    public function addDebating(SubComment $debating): static
    {
        if (!$this->debating->contains($debating)) {
            $this->debating->add($debating);
            $debating->setSubCommentUser($this);
        }

        return $this;
    }

    public function removeDebating(SubComment $debating): static
    {
        if ($this->debating->removeElement($debating)) {
            // set the owning side to null (unless already changed)
            if ($debating->getSubCommentUser() === $this) {
                $debating->setSubCommentUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Network>
     */
    public function getUserNetwork(): Collection
    {
        return $this->user_network;
    }

    public function addUserNetwork(Network $userNetwork): static
    {
        if (!$this->user_network->contains($userNetwork)) {
            $this->user_network->add($userNetwork);
            $userNetwork->setNetworkUser($this);
        }

        return $this;
    }

    public function removeUserNetwork(Network $userNetwork): static
    {
        if ($this->user_network->removeElement($userNetwork)) {
            // set the owning side to null (unless already changed)
            if ($userNetwork->getNetworkUser() === $this) {
                $userNetwork->setNetworkUser(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->pseudo;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
