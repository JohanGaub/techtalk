<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/home', name: 'home', methods: [Request::METHOD_GET], priority: 1)]
    public function home(): Response
    {
        // Check if the user has the ROLE_ADMIN
        if (!$this->isGranted('ROLE_ADMIN')) {
            return throw new AccessDeniedException('Only admins can access this page.');
        }

        return $this->render('home/home.html.twig', [
            'controller_name' => self::class,
        ]);
    }
}
