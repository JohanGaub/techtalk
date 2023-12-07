<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Topic;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

class TopicReviewSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function guardReview(GuardEvent $event): void
    {
        /** @var Topic $topic */
        $topic = $event->getSubject();

        if ($this->security->isGranted('ROLE_USER_BOARD') && $topic->getCurrentPlace() === 'reviewed') {
            $topic->setUserReviewer($this->security->getUser());
            $topic->setReviewedAt(new \DateTimeImmutable());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.topic_publishing.guard.to_review' => ['guardReview'],
        ];
    }
}
