<?php

namespace App\Controller;

use App\Form\LoginCheckType;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
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
    private const EMAIL_STATUS_SUCCESS = 'success';
    private const EMAIL_STATUS_FAILURE = 'failure';

    #[Route('/login_link', name: 'login_link', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function loginLink(
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

                // create a notification based on the login link details
                $notification = new LoginLinkNotification(
                    $loginLinkDetails,
                    'Link to connect to Techtalk website!' // email subject
                );
                // create a recipient for this user
                $recipient = new Recipient($user->getEmail());

                // send the notification to the user
                $notifier->send($notification, $recipient);
            } catch (ExpiredLoginLinkException $e) {
                return new Response('Login link has expired');
            } catch (TransportExceptionInterface $e) {
                return new Response('Failed to send email');
            }

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig', [
                'user_email' => $user->getEmail(),
            ]);
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login_link.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route('/login_links', name: 'login_links', methods: Request::METHOD_POST)]
    public function loginLinks(NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        LoggerInterface $logger
    ): Response {
        $processedEmails = [];

        foreach ($userRepository->findAll() as $user) {
            $userEmail = $user->getEmail();

            try {
                // create a login link for $user this returns an instance of LoginLinkDetails
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                // create a notification based on the login link details
                $notification = new LoginLinkNotification(
                    $loginLinkDetails,
                    'Link to connect to Techtalk website!' // email subject
                );

                // create a recipient for this user
                $recipient = new Recipient($userEmail);

                // send the notification to the user
                $notifier->send($notification, $recipient);

                // If the email is sent successfully, add it to the output array with a success status
                $processedEmails[self::EMAIL_STATUS_SUCCESS][] = ['email' => $userEmail];
            } catch (ExpiredLoginLinkException|TransportExceptionInterface $e) {
                // If the link is expired or has a transport exception, log the error and add the email to the output array with a failure status
                $logger->error(sprintf('Failed to send login link to %s: %s', $userEmail, $e->getMessage()));
                $processedEmails[self::EMAIL_STATUS_FAILURE][] = ['email' => $userEmail, 'error' => $e->getMessage()];
            }
        }
        // render a "Login link is sent!" page
        return $this->render('security/login_links_sent.html.twig', [
            'success_emails' => $processedEmails[self::EMAIL_STATUS_SUCCESS] ?? [],
            'failure_emails' => $processedEmails[self::EMAIL_STATUS_FAILURE] ?? []
        ]);
    }


    /**
     * After clicking on the login link, the user is redirected to this route.
     */
    #[
        Route('/login_check', name: 'login_check')]
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
