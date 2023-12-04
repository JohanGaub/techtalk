<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $LastName = null;

    #[ORM\Column(nullable: false)]
    private ?bool $enabled = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Agency $agency = null;

    #[ORM\OneToMany(mappedBy: 'userProposerId', targetEntity: Topic::class)]
    private Collection $proposedTopics;

    #[ORM\OneToMany(mappedBy: 'userValidatorId', targetEntity: Topic::class)]
    private Collection $validatedTopics;

    #[ORM\OneToMany(mappedBy: 'userPresenterId', targetEntity: Topic::class)]
    private Collection $presentedTopics;

    public function __construct()
    {
        $this->proposedTopics = new ArrayCollection();
        $this->validatedTopics = new ArrayCollection();
        $this->presentedTopics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
        return (string)$this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getAgency(): ?Agency
    {
        return $this->agency;
    }

    public function setAgency(?Agency $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getProposedTopics(): Collection
    {
        return $this->proposedTopics;
    }

    public function addProposedTopic(Topic $proposedTopic): self
    {
        if (!$this->proposedTopics->contains($proposedTopic)) {
            $this->proposedTopics->add($proposedTopic);
            $proposedTopic->setUserProposer($this);
        }

        return $this;
    }

    public function removeProposedTopic(Topic $proposedTopic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->proposedTopics->removeElement($proposedTopic) && $proposedTopic->getUserProposer() === $this) {
            $proposedTopic->setUserProposer(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getValidatedTopics(): Collection
    {
        return $this->validatedTopics;
    }

    public function addValidatedTopic(Topic $validatedTopic): self
    {
        if (!$this->validatedTopics->contains($validatedTopic)) {
            $this->validatedTopics->add($validatedTopic);
            $validatedTopic->setUserValidator($this);
        }

        return $this;
    }

    public function removeValidatedTopic(Topic $validatedTopic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->validatedTopics->removeElement($validatedTopic) && $validatedTopic->getUserValidator() === $this) {
            $validatedTopic->setUserValidator(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getPresentedTopics(): Collection
    {
        return $this->presentedTopics;
    }

    public function addPresentedTopic(Topic $presentedTopic): self
    {
        if (!$this->presentedTopics->contains($presentedTopic)) {
            $this->presentedTopics->add($presentedTopic);
            $presentedTopic->setUserPresenter($this);
        }

        return $this;
    }

    public function removePresentedTopic(Topic $presentedTopic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->presentedTopics->removeElement($presentedTopic) && $presentedTopic->getUserPresenter() === $this) {
            $presentedTopic->setUserPresenter(null);
        }

        return $this;
    }
}
