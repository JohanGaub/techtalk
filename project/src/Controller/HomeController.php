<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(Request $request, Security $security): Response {

        return $this->render(
            'home/home.html.twig',
            [
                'user_email' => $security->getUser() ? $security->getUser()->getUserIdentifier() : $request->query->get('userEmail'),
            ]
        );
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response {
        return $this->redirectToRoute('home');
    }
}