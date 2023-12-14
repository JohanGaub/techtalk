<?php

namespace App\Entity;

use App\Repository\MeetupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: MeetupRepository::class)]
class Meetup
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $capacity = null;

    #[ORM\ManyToOne(inversedBy: 'meetups')]
    private ?Agency $agency = null;

    #[ORM\ManyToOne(inversedBy: 'meetups')]
    private ?User $organizer = null;

    #[ORM\OneToMany(mappedBy: 'meetup', targetEntity: Topic::class)]
    private Collection $topics;

    #[ORM\OneToMany(mappedBy: 'meetup', targetEntity: MeetupUserParticipant::class)]
    private Collection $userMeetups;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->userMeetups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): self
    {
        $this->capacity = $capacity;

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

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setMeetup($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): self
    {
        // set the owning side to null (unless already changed)
        if ($this->topics->removeElement($topic) && $topic->getMeetup() === $this) {
            $topic->setMeetup(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, MeetupUserParticipant>
     */
    public function getUserMeetups(): Collection
    {
        return $this->userMeetups;
    }

    public function addUserMeetup(MeetupUserParticipant $userMeetup): static
    {
        if (!$this->userMeetups->contains($userMeetup)) {
            $this->userMeetups->add($userMeetup);
            $userMeetup->setMeetup($this);
        }

        return $this;
    }

    public function removeUserMeetup(MeetupUserParticipant $userMeetup): static
    {
        // set the owning side to null (unless already changed)
        if ($this->userMeetups->removeElement($userMeetup) && $userMeetup->getMeetup() === $this) {
            $userMeetup->setMeetup(null);
        }

        return $this;
    }
}
