<?php

declare(strict_types=1);

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

    #[ORM\OneToMany(mappedBy: 'userProposer', targetEntity: Topic::class)]
    private Collection $proposedTopics;

    #[ORM\OneToMany(mappedBy: 'userReviewer', targetEntity: Topic::class)]
    private Collection $reviewedTopics;

    #[ORM\OneToMany(mappedBy: 'userPresenter', targetEntity: Topic::class)]
    private Collection $presentedTopics;

    /**
     * If a user is removed, all their votes should be removed as well. That's why we use orphanRemoval: true.
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserTopicVote::class, orphanRemoval: true)]
    private Collection $userTopicVotes;

    #[ORM\OneToMany(mappedBy: 'organizer', targetEntity: Meetup::class)]
    private Collection $meetups;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MeetupUserParticipant::class)]
    private Collection $meetupUserParticipants;

    public function __construct()
    {
        $this->proposedTopics = new ArrayCollection();
        $this->reviewedTopics = new ArrayCollection();
        $this->presentedTopics = new ArrayCollection();
        $this->userTopicVotes = new ArrayCollection();
        $this->meetups = new ArrayCollection();
        $this->meetupUserParticipants = new ArrayCollection();
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
    public function getReviewedTopics(): Collection
    {
        return $this->reviewedTopics;
    }

    public function addReviewedTopic(Topic $reviewedTopic): self
    {
        if (!$this->reviewedTopics->contains($reviewedTopic)) {
            $this->reviewedTopics->add($reviewedTopic);
            $reviewedTopic->setUserReviewer($this);
        }

        return $this;
    }

    public function removeReviewedTopic(Topic $reviewedTopic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->reviewedTopics->removeElement($reviewedTopic) && $reviewedTopic->getUserReviewer() === $this) {
            $reviewedTopic->setUserReviewer(null);
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
     * @return Collection<int, UserTopicVote>
     */
    public function getUserTopicVotes(): Collection
    {
        return $this->userTopicVotes;
    }

    public function addUserTopicVote(UserTopicVote $userTopicVote): self
    {
        if (!$this->userTopicVotes->contains($userTopicVote)) {
            $this->userTopicVotes->add($userTopicVote);
            $userTopicVote->setUser($this);
        }

        return $this;
    }

    public function removeUserTopicVote(UserTopicVote $userTopicVote): self
    {
        // set the owning side to null (unless already changed)
        if ($this->userTopicVotes->removeElement($userTopicVote) && $userTopicVote->getUser() === $this) {
            $userTopicVote->setUser(null);
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

    public function addMeetup(Meetup $meetup): self
    {
        if (!$this->meetups->contains($meetup)) {
            $this->meetups->add($meetup);
            $meetup->setOrganizer($this);
        }

        return $this;
    }

    public function removeMeetup(Meetup $meetup): self
    {
        // set the owning side to null (unless already changed)
        if ($this->meetups->removeElement($meetup) && $meetup->getOrganizer() === $this) {
            $meetup->setOrganizer(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, MeetupUserParticipant>
     */
    public function getMeetupUserParticipants(): Collection
    {
        return $this->meetupUserParticipants;
    }

    public function addUserMeetup(MeetupUserParticipant $userMeetup): static
    {
        if (!$this->meetupUserParticipants->contains($userMeetup)) {
            $this->meetupUserParticipants->add($userMeetup);
            $userMeetup->setUser($this);
        }

        return $this;
    }

    public function removeMeetupUserParticipant(MeetupUserParticipant $meetupUserParticipant): static
    {
        // set the owning side to null (unless already changed)
        if ($this->meetupUserParticipants->removeElement($meetupUserParticipant) && $meetupUserParticipant->getUser() === $this) {
            $meetupUserParticipant->setUser(null);
        }

        return $this;
    }
}
