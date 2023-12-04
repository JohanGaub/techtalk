<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $currentPlace = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $presentedAt = null;

    #[ORM\ManyToOne(inversedBy: 'proposedTopics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userProposer = null;

    #[ORM\ManyToOne(inversedBy: 'validatedTopics')]
    private ?User $userValidator = null;

    #[ORM\ManyToOne(inversedBy: 'presentedTopics')]
    private ?User $userPresenter = null;

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

    public function getCurrentPlace(): ?string
    {
        return $this->currentPlace;
    }

    public function setCurrentPlace(string $currentPlace): self
    {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeImmutable $validatedAt): self
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    public function getPresentedAt(): ?\DateTimeImmutable
    {
        return $this->presentedAt;
    }

    public function setPresentedAt(?\DateTimeImmutable $presentedAt): self
    {
        $this->presentedAt = $presentedAt;

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

    public function getUserValidator(): ?User
    {
        return $this->userValidator;
    }

    public function setUserValidator(?User $userValidator): self
    {
        $this->userValidator = $userValidator;

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
}
