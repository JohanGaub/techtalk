<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Topic;
use App\Form\EditTopicType;
use App\Form\NewTopicType;
use App\Repository\TopicRepository;
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
        private readonly WorkflowInterface $workflow
    ) {
    }


    #[Route('/', name: 'topic_index', methods: [Request::METHOD_GET])]
    public function index(TopicRepository $topicRepository): Response
    {
        return $this->render('topic/index.html.twig', [
            'topics' => $topicRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'topic_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $topic = new Topic();

        // Apply the 'to_review' transition which will set the currentPlace to 'draft'
        if ($this->workflow->can($topic, 'to_review')) {
            $this->workflow->apply($topic, 'to_review');
        }

        $form = $this->createForm(NewTopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setUserProposer($this->getUser());
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
        $form = $this->createForm(EditTopicType::class, $topic);
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
        if ($this->isCsrfTokenValid('delete'.$topic->getId(), $request->request->get('_token'))) {
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('topic_index', [], Response::HTTP_SEE_OTHER);
    }
}
