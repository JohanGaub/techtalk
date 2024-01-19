<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\LoginLinkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function forgotPassword(
        Request          $request,
        LoginLinkService $loginLinkService,
        UserRepository   $userRepository
    ): Response {
        $form = $this->createForm(EmailType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();

            if (null !== $userRepository->findOneBy(['email' => $email])) {
                /**
                 * Set the suitable message in the session for the login-check page.
                 */
                $session = $request->getSession();
                $session->set('message', 'security.login.check.access.reset_password_page');

                $loginLinkService->sendLoginLink($email, 'Link to access the reset password page.');
                $this->addFlash('success', [
                    'message' => 'security.forgot_password.link_sent',
                    'params' => ['%email%' => $email]
                ]);
                return $this->redirectToRoute('login');
            }

            $this->addFlash('error', [
                'message' => 'Username could not be found.',
                'params' => []
            ]);
            return $this->redirectToRoute('login');
        }

        return $this->render('forgot_password/form.html.twig', [
            'form' => $form,
        ]);
    }
}
