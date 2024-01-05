<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginCheckType;
use App\Form\UserFileType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\LoginLinkService;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class SecurityController extends AbstractController
{
    private const STATUS_SUCCESS = 'success';

    private const STATUS_FAILURE = 'failure';

    #[Route('/login_link', name: 'login_link', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function loginLink(
        LoginLinkService $loginLinkService,
        Request          $request
    ): Response {
        // check if login form is submitted
        if ($request->isMethod(Request::METHOD_POST)) {
            $postedEmail = $request->request->get('email');

            $loginLinkService->sendLoginLink($postedEmail);

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig', [
                'user_email' => $postedEmail,
            ]);
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/login_link.html.twig', [
            'controller_name' => self::class,
            // créer les formulaires en php pour cette page et les ajouter ici.
        ]);
    }

    #[Route('/login_links', name: 'login_links', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function loginLinks(
        NotifierInterface         $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository            $userRepository,
        LoggerInterface           $logger,
        Request                   $request
    ): Response {
        $processedEmails = [];

        if ($request->isMethod(Request::METHOD_POST)) {
            foreach ($userRepository->findAll() as $user) {
                try {
                    // create a login link for $user this returns an instance of LoginLinkDetails
                    $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                    // create a notification based on the login link details
                    $notification = new LoginLinkNotification(
                        $loginLinkDetails,
                        'Link to connect to Techtalk website!' // email subject
                    );

                    // create a recipient for this user
                    $userEmail = $user->getEmail();
                    $recipient = new Recipient($userEmail);

                    // send the notification to the user
                    $notifier->send($notification, $recipient);

                    // If the email is sent successfully, add it to the output array with a success status
                    $processedEmails[self::STATUS_SUCCESS][] = ['email' => $userEmail];
                } catch (ExpiredLoginLinkException|TransportExceptionInterface $e) {
                    // If the link is expired or has a transport exception,
                    // log the error and add the email to the output array with a failure status
                    $logger->error(sprintf('Failed to send login link to %s: %s', $userEmail, $e->getMessage()));
                    $processedEmails[self::STATUS_FAILURE][] = ['email' => $userEmail, 'error' => $e->getMessage()];
                }
            }

            // render a "Login link is sent!" page
            return $this->render('security/login_links_sent.html.twig', [
                self::STATUS_SUCCESS => $processedEmails[self::STATUS_SUCCESS] ?? [],
                self::STATUS_FAILURE => $processedEmails[self::STATUS_FAILURE] ?? [],
                'total_success' => count($processedEmails[self::STATUS_SUCCESS] ?? []),
                'total_failure' => count($processedEmails[self::STATUS_FAILURE] ?? []),
            ]);
        }

        // render a "Login link is sent!" page
        return $this->render('security/login_links.html.twig', [
            'controller_name' => self::class,
        ]);
    }

    /**
     * After clicking on the login link, the user is redirected to this route.
     */
    #[Route('/login_check', name: 'login_check')]
    public function check(Request $request): Response
    {
        $form = $this->createForm(LoginCheckType::class, [
            "expires" => $request->query->get('expires'),
            "user_email" => $request->query->get('user'),
            "hash" => $request->query->get('hash'),
        ]);

        // render a template with the button
        // see https://symfony.com/doc/current/forms.html#rendering-forms :
        // from SF 6.2, render() method calls $form->createView() to transform the form into a form view instance.
        return $this->render('security/process_login_link.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/upload_users', name: 'upload_users', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function uploadUsers(
        Request      $request,
        FileUploader $fileUploaderService,
        UserService  $userService
    ): Response {
        $form = $this->createForm(UserFileType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $usersFile */
            $usersFile = $form->get('users')->getData();
            if ($usersFile) {
                $fileName = $fileUploaderService->upload($usersFile);
                // Add users from the file inside the database.
                $uploadResults = $userService->addUsers($fileName);
            }

            return $this->render('security/login_links.html.twig', [
                'upload_users_output' => $fileName ?? [],
                'controller_name' => self::class,
                'upload_results' => $uploadResults ?? [],
//                'success_uploads' => $processedUploads[self::EMAIL_STATUS_SUCCESS] ?? [],
//                'failure_uploads' => $processedUploads[self::EMAIL_STATUS_FAILURE] ?? []
            ]);
        }

        // see https://symfony.com/doc/current/forms.html#rendering-forms :
        // from SF 6.2, render() method calls $form->createView() to transform the form into a form view instance.
        return $this->render('security/upload_users.html.twig', [
            'upload_form' => $form,
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/logout', name: 'logout', methods: [Request::METHOD_GET])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new AccessDeniedException("Don't forget to activate logout in security.yaml");
    }
}
