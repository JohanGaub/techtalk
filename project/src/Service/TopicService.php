<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Topic;
use App\Enum\CurrentPlace;
use App\Exception\TopicStateException;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LogLevel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

readonly class TopicService
{
    public function __construct(
        #[Target('topic_publishing')]
        private WorkflowInterface      $workflow,
        private TopicRepository        $topicRepository,
        private EntityManagerInterface $entityManager,
        private Security               $security,
        private LoggerService          $loggerService
    ) {
    }

    public function getTopics(array $roles): array
    {
        return in_array('ROLE_BOARD_USER', $roles)
            ? $this->topicRepository->getTopicsForBoardUser()
            : $this->topicRepository->getTopicsForUser();
    }

    public function review(Topic $topic): void
    {
        $this->doTransition('to_review', $topic);
        $topic->setCurrentPlace(CurrentPlace::IN_REVIEW->value);
        $topic->setInReviewAt(new \DateTimeImmutable());

        $this->entityManager->persist($topic);
    }

    public function publish(Topic $topic): void
    {
        $this->doTransition('publish', $topic);
        $topic->setCurrentPlace(CurrentPlace::PUBLISHED->value);
        $topic->setPublishedAt(new \DateTimeImmutable());
        $topic->setUserPublisher($this->security->getUser());

        $this->entityManager->persist($topic);
    }

    public function rejectToDraft(Topic $topic): void
    {
        $this->doTransition('reject_to_draft', $topic);
        $topic->setCurrentPlace(CurrentPlace::DRAFT->value);
        $topic->setUserPublisher(null);

        $this->entityManager->persist($topic);
    }


    /**
     * @throws TopicStateException
     */
    private function doTransition(string $transition, Topic $topic): void
    {
        try {
            $this->workflow->apply($topic, $transition);
            $this->loggerService->log(
                LogLevel::INFO,
                'Transition %s applied to topic with ID: %s.',
                [$transition, $topic->getId()]
            );
        } catch (LogicException $logicException) {
            $this->loggerService->log(
                LogLevel::ERROR,
                'Failed to apply transition %s to topic with ID: %s.',
                [$transition, $topic->getId()],
                $logicException
            );
            // Throw a custom exception here and handle this in your controller,
            // to show an error message to the user
            throw new TopicStateException(
                sprintf(
                    'Cannot change the state of the topic, because %s',
                    $logicException->getMessage()
                ),
                0,
                $logicException
            );
        }
    }
}
