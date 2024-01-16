<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Service\HtmlToPdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PdfController extends AbstractController
{
    public function __construct(
        private readonly HtmlToPdfService $htmlToPdfService,
    ) {
    }

    #[Route('/pdf', name: 'pdf')]
    public function index(Request $request, TopicRepository $topicRepository): Response
    {
        $templateName = $request->get('templateName');
        $topicIds = $request->get('data')['topicIds'];

        $topics = $topicRepository->findBy(['id' => $topicIds]);

        $html = $this->renderView($templateName, ['topics' => $topics]);
        $pdf = $this->htmlToPdfService->render($html);

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="hello.pdf"',
//            'Content-Disposition' => sprintf('inline; filename="%s.pdf"', $this->slugger->slug($templateName))
        ]);
    }
}
