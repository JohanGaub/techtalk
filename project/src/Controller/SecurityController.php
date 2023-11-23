<?php

namespace App\Controller;

use App\Form\LoginCheckType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function requestLoginLink(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request
    ): Response {
        // check if login form is submitted
        if ($request->isMethod(Request::METHOD_POST)) {
            // load the user in some way (e.g. using the form input)
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                throw new UserNotFoundException('User not found');
            }

            try {
                // create a login link for $user this returns an instance of LoginLinkDetails
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
            } catch (ExpiredLoginLinkException $e) {
                return new Response('Login link has expired');
            }

            // create a notification based on the login link details
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Link to connect to Techtalk website!' // email subject
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

//            $loginLink = $loginLinkDetails->getUrl();

            try {
                // send the notification to the user
                $notifier->send($notification, $recipient);
            } catch (TransportExceptionInterface $e) {
                // handle the exception and return a failure message
                return new Response('Failed to send email');
            }

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig', [
                'user_email' => $user->getEmail(),
            ]);
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    /**
     * After clicking on the login link, the user is redirected to this route.
     */
    #[Route('/login_check', name: 'login_check')]
    public function check(Request $request): Response {

        $form = $this->createForm(LoginCheckType::class, [
            "expires" => $request->query->get('expires'),
            "user_email" => $request->query->get('user'),
            "hash" => $request->query->get('hash'),
        ]);

        // render a template with the button
        // SF 6.2 : render() method calls $form->createView() to transform the form into a form view instance.
        return $this->render('security/process_login_link.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): never {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
