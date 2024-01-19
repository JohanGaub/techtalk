<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
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
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isEnabled = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Agency $agency = null;

    #[ORM\OneToMany(mappedBy: 'userProposer', targetEntity: Topic::class)]
    private Collection $proposedTopics;

    #[ORM\OneToMany(mappedBy: 'userPublisher', targetEntity: Topic::class)]
    private Collection $publishedTopics;

    #[ORM\OneToMany(mappedBy: 'userPresenter', targetEntity: Topic::class)]
    private Collection $presentedTopics;

    #[ORM\OneToMany(mappedBy: 'userOrganiser', targetEntity: Meetup::class)]
    private Collection $organisedMeetups;

    #[ORM\ManyToMany(targetEntity: Meetup::class, mappedBy: 'users')]
    private Collection $meetups;

    #[ORM\ManyToMany(targetEntity: Topic::class, mappedBy: 'users')]
    private Collection $topics;

    public function __construct()
    {
        $this->proposedTopics = new ArrayCollection();
        $this->publishedTopics = new ArrayCollection();
        $this->presentedTopics = new ArrayCollection();
        $this->organisedMeetups = new ArrayCollection();
        $this->meetups = new ArrayCollection();
        $this->topics = new ArrayCollection();
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
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

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
    public function getPublishedTopics(): Collection
    {
        return $this->publishedTopics;
    }

    public function addPublishedTopic(Topic $publishedTopic): self
    {
        if (!$this->publishedTopics->contains($publishedTopic)) {
            $this->publishedTopics->add($publishedTopic);
            $publishedTopic->setUserPublisher($this);
        }

        return $this;
    }

    public function removePublishedTopic(Topic $publishedTopic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->publishedTopics->removeElement($publishedTopic) && $publishedTopic->getUserPublisher() === $this) {
            $publishedTopic->setUserPublisher(null);
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

    /**
     * @return Collection<int, Meetup>
     */
    public function getOrganisedMeetups(): Collection
    {
        return $this->organisedMeetups;
    }

    public function addOrganisedMeetup(Meetup $organisedMeetup): self
    {
        if (!$this->organisedMeetups->contains($organisedMeetup)) {
            $this->organisedMeetups->add($organisedMeetup);
            $organisedMeetup->setUserOrganiser($this);
        }

        return $this;
    }

    public function removeOrganisedMeetup(Meetup $organisedMeetup): self
    {
        // set the owning side to null (unless already changed)
        if (
            $this->organisedMeetups->removeElement($organisedMeetup)
            && $organisedMeetup->getUserOrganiser() === $this
        ) {
            $organisedMeetup->setUserOrganiser(null);
        }

        return $this;
    }


    /**
     * @return Collection<int, Meetup>
     */
    public function getMeetups(): Collection
    {
        return $this->meetups;
    }

    public function addMeetup(Meetup $meetup): static
    {
        if (!$this->meetups->contains($meetup)) {
            $this->meetups->add($meetup);
            $meetup->addUser($this);
        }

        return $this;
    }

    public function removeMeetup(Meetup $meetup): static
    {
        if ($this->meetups->removeElement($meetup)) {
            $meetup->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->addUser($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            $topic->removeUser($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getEmail() ?? '';
    }
}
