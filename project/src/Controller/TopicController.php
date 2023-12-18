<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Topic;
use App\Form\TopicType;
use App\Service\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/topic', name: 'topic_')]
class TopicController extends AbstractController
{
    public function __construct(
        #[Target('topic_publishing')]
        private readonly WorkflowInterface $workflow,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'index', methods: [Request::METHOD_GET])]
    public function index(TopicService $topicService): Response
    {
        $topics = $topicService->getTopics($this->getUser()->getRoles());

        return $this->render('topic/index.html.twig', [
            'topics' => $topics,
        ]);
    }

    #[Route('/create', name: 'create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request): Response
    {
        $topic = new Topic();

        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setUserProposer($this->getUser());
            $this->workflow->getMarking($topic);
            $this->entityManager->persist($topic);
            $this->entityManager->flush();

            return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('topic/new.html.twig', [
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: [Request::METHOD_GET])]
    public function show(Topic $topic): Response
    {
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Topic $topic): Response
    {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('topic/edit.html.twig', [
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, Topic $topic): Response
    {
        if ($this->isCsrfTokenValid('delete' . $topic->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($topic);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * We're using Workflow Component.
     * When we're in transition "ask_for_review". We're passing from "draft" to "in_review" current place.
     * When we're in transition "reject_to_draft". We're passing from "in_review" to "draft" current place.
     * When we're in transition "publish". We're passing from "in_review" to "published" current place.
     * When we're in transition "roll_back_to_review". We're rolling back from "published" to "in_review" current place.
     */
    #[Route('/{id}/{transitionName}', name: 'do_transition', methods: [Request::METHOD_POST])]
    public function doTransition(Request $request, Topic $topic, string $transitionName): Response
    {
        if ($this->isCsrfTokenValid(sprintf('%s%d', $transitionName, $topic->getId()), $request->request->get('_token'))) {
            $this->workflow->apply($topic, $transitionName);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }
}
