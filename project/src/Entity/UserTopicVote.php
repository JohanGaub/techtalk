<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserTopicVoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: UserTopicVoteRepository::class)]
#[ORM\Table(name: "user_topic_vote", uniqueConstraints: [
    new UniqueConstraint(name: "user_topic_unique", columns: ["user_id", "topic_id"])
])]
class UserTopicVote
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userTopicVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Topic $topic = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'userTopicVotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
