<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Topic;
use App\Enum\CurrentPlace;
use App\Form\TopicType;
use App\Repository\TopicRepository;
use App\Service\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/topic')]
class TopicController extends AbstractController
{
    public function __construct(
        #[Target('topic_publishing')]
        private readonly WorkflowInterface $workflow,
        private readonly TopicService $topicService
    ) {
    }

    #[Route('/', name: 'topic_index', methods: [Request::METHOD_GET])]
    public function index(TopicRepository $topicRepository): Response
    {
        $topics = $this->topicService->getTopics($this->getUser()->getRoles());

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    #[Route('/create', name: 'topic_create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $topic = new Topic();

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setUserProposer($this->getUser());
            $this->workflow->getMarking($topic);
            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('topic/new.html.twig', [
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'topic_show', methods: [Request::METHOD_GET])]
    public function show(Topic $topic): Response
    {
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }

    #[Route('/{id}/edit', name: 'topic_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('topic/edit.html.twig', [
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'topic_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $topic->getId(), $request->request->get('_token'))) {
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * We're using Workflow Component. We're in transition "reject_to_draft".
     * We're passing from "reviewed" to "draft" current place.
     */
    #[Route('/{id}/reject-to-draft', name: 'topic_reject_to_draft', methods: [Request::METHOD_POST])]
    public function rejectedToDraft(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject_to_draft' . $topic->getId(), $request->request->get('_token'))) {
            $this->workflow->apply($topic, 'reject_to_draft');
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * We're in transition "ask_for_review".
     * We're passing from "draft" to "reviewed" current place.
     */
    #[Route('/{id}/ask-for-review', name: 'topic_ask_for_review', methods: [Request::METHOD_POST])]
    public function askForReview(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('ask_for_review' . $topic->getId(), $request->request->get('_token'))) {
            $this->workflow->apply($topic, 'ask_for_review');

            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * We're in transition "publish".
     * We're passing from "reviewed" to "published" current place.
     */
    #[Route('/{id}/publish', name: 'topic_publish', methods: [Request::METHOD_POST])]
    public function publish(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('publish' . $topic->getId(), $request->request->get('_token'))) {
            $this->workflow->apply($topic, 'publish');
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * We're in transition "roll_back_to_review".
     * We're rolling back from "published" to "reviewed" current place.
     */
    #[Route('/{id}/roll-back-to-review', name: 'topic_roll_back_to_review', methods: [Request::METHOD_POST])]
    public function goBackToReview(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('roll_back_to_review' . $topic->getId(), $request->request->get('_token'))) {
            $this->workflow->apply($topic, 'roll_back_to_review');

            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }
}
