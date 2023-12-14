<?php

namespace App\Entity;

use App\Repository\UserMeetupRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserMeetupRepository::class)]
#[ORM\Table(name: "meetup_user_participant", uniqueConstraints: [
    new UniqueConstraint(name: "meetup_user_unique", columns: ["meetup_id", "user_id"])
])]
class MeetupUserParticipant
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userMeetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userMeetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Meetup $meetup = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
