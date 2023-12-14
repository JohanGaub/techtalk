<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Topic;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class TopicPublishingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function onAskForReview(TransitionEvent $event): void
    {
        /** @var Topic $topic */
        $topic = $event->getSubject();

        $topic->setReviewedAt(new \DateTimeImmutable());
    }

    public function onRejectToDraft(TransitionEvent $event): void
    {
        /** @var Topic $topic */
        $topic = $event->getSubject();

        $topic->setReviewedAt(null);
    }

    public function onPublish(TransitionEvent $event): void
    {
        /** @var Topic $topic */
        $topic = $event->getSubject();

        $topic->setUserReviewer($this->security->getUser());
        $topic->setPublishedAt(new \DateTimeImmutable());
    }

    public function onRollBackToReview(TransitionEvent $event): void
    {
        /** @var Topic $topic */
        $topic = $event->getSubject();

        $topic->setUserReviewer(null);
        $topic->setPublishedAt(null);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.topic_publishing.transition.ask_for_review' => ['onAskForReview'],
            'workflow.topic_publishing.transition.reject_to_draft' => ['onRejectToDraft'],
            'workflow.topic_publishing.transition.publish' => ['onPublish'],
            'workflow.topic_publishing.transition.roll_back_to_review' => ['onRollBackToReview'],
        ];
    }
}
