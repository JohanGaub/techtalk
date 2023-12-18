<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Topic;
use App\Enum\CurrentPlace;
use App\Exception\TopicStateException;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

class TopicService
{
    public function __construct(
        #[Target('topic_publishing')]
        private readonly WorkflowInterface $workflow,
        private readonly TopicRepository $topicRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {
    }

    public function getTopics(array $roles): array
    {
        if (in_array('ROLE_BOARD_USER', $roles)) {
            return $this->topicRepository->getTopicsForBoardUser();
        }

        return $this->topicRepository->getTopicsForUser();
    }

    public function review(Topic $topic): void
    {
        $this->doTransition('to_review', $topic);
        $topic->setCurrentPlace(CurrentPlace::IN_REVIEW->value);
        $topic->setInReviewAt(new \DateTimeImmutable());

        $this->entityManager->persist($topic);
        //        $this->entityManager->flush();
    }

    public function publish(Topic $topic): void
    {
        $this->doTransition('publish', $topic);
        $topic->setCurrentPlace(CurrentPlace::PUBLISHED->value);
        $topic->setPublishedAt(new \DateTimeImmutable());
        $topic->setUserPublisher($this->security->getUser());

        $this->entityManager->persist($topic);
        //        $this->entityManager->flush();
    }

    public function rejectToDraft(Topic $topic): void
    {
        $this->doTransition('reject_to_draft', $topic);
        $topic->setCurrentPlace(CurrentPlace::DRAFT->value);
        $topic->setUserPublisher(null);

        $this->entityManager->persist($topic);
        //        $this->entityManager->flush();
    }


    /**
     * @throws TopicStateException
     */
    private function doTransition(string $transition, Topic $topic): void
    {
        try {
            $this->workflow->apply($topic, $transition);
        } catch (LogicException $logicException) {
            // Throw a custom exception here and handle this in your controller,
            // to show an error message to the user
            throw new TopicStateException(sprintf('Cannot change the state of the topic, because %s', $logicException->getMessage()), 0, $logicException);
        }
    }
}
