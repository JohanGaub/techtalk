<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DurationCategory;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateInterval $duration = null;

    #[ORM\Column(nullable: true, enumType: durationCategory::class)]
    private ?durationCategory $durationCategory = null;

    #[ORM\Column]
    private string $currentPlace;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $inReviewAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'proposedTopics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userProposer = null;

    #[ORM\ManyToOne(inversedBy: 'publishedTopics')]
    private ?User $userPublisher = null;

    #[ORM\ManyToOne(inversedBy: 'presentedTopics')]
    private ?User $userPresenter = null;

    #[ORM\ManyToOne(inversedBy: 'topics')]
    private ?Meetup $meetup = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'topics')]
    #[ORM\JoinTable(name: 'topics_users_vote')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(?\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDurationCategory(): ?DurationCategory
    {
        return $this->durationCategory;
    }

    public function setDurationCategory(?DurationCategory $durationCategory): self
    {
        $this->durationCategory = $durationCategory;

        return $this;
    }

    public function getCurrentPlace(): string
    {
        return $this->currentPlace;
    }

    /**
     * @see https://symfony.com/doc/current/components/workflow.html#creating-a-workflow
     * You don't need to set the initial marking in the constructor or any other method;
     * this is configured in the workflow with the 'initial_marking' option.
     */
    public function setCurrentPlace(string $currentPlace): self
    {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function getInReviewAt(): ?\DateTimeImmutable
    {
        return $this->inReviewAt;
    }

    public function setInReviewAt(?\DateTimeImmutable $inReviewAt): self
    {
        $this->inReviewAt = $inReviewAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getUserProposer(): ?User
    {
        return $this->userProposer;
    }

    public function setUserProposer(?User $userProposer): self
    {
        $this->userProposer = $userProposer;

        return $this;
    }

    public function getUserPublisher(): ?User
    {
        return $this->userPublisher;
    }

    public function setUserPublisher(?User $userPublisher): self
    {
        $this->userPublisher = $userPublisher;

        return $this;
    }

    public function getUserPresenter(): ?User
    {
        return $this->userPresenter;
    }

    public function setUserPresenter(?User $userPresenter): self
    {
        $this->userPresenter = $userPresenter;

        return $this;
    }

    public function getMeetup(): ?Meetup
    {
        return $this->meetup;
    }

    public function setMeetup(?Meetup $meetup): self
    {
        $this->meetup = $meetup;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function __toString(): string
    {
        return sprintf("%s - %s", $this->getName(), $this->getDescription()) ?? '';
    }
}
