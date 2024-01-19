<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginCheckType;
use App\Form\ResetPasswordType;
use App\Form\UserFileType;
use App\Repository\UserRepository;
use App\Service\FileUploaderService;
use App\Service\LoginLinkService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class SecurityController extends AbstractController
{
    private const STATUS_SUCCESS = 'success';

    private const STATUS_FAILURE = 'failure';

    #[Route('/login-link', name: 'login_link', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function loginLink(
        Request          $request,
        LoginLinkService $loginLinkService,
        UserRepository   $userRepository
    ): Response {
        $form = $this->createForm(EmailType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData();

            if (null !== $userRepository->findOneBy(['email' => $email])) {
                $loginLinkService->sendLoginLink($email, 'Link to connect to Techtalk website!');
                $this->addFlash('success', [
                    'message' => 'security.login.link_sent',
                    'params' => ['%email%' => $email]
                ]);
                return $this->render('security/login_link_sent.html.twig', [
                    'user_email' => $email,
                ]);
            }

            $this->addFlash('error', [
                'message' => 'Username could not be found.',
                'params' => []
            ]);
            return $this->redirectToRoute('login');
        }

        // if it's not submitted, render the "login_link" form
        return $this->render('security/login_link.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/login-links', name: 'login_links', methods: [Request::METHOD_GET, Request::METHOD_POST])]
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
     * Inside its email, after clicking on the login link, the user is redirected to this route.
     */
    #[Route('/login-check', name: 'login_check')]
    public function loginCheck(
        Request $request,
    ): Response {

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
            'message' => $request->getSession()->get('message'),
        ]);
    }

    #[Route('/reset-password', name: 'reset_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function resetPassword(
        #[CurrentUser] User    $user,
        Request                $request,
        EntityManagerInterface $entityManager,
        Security               $security,
    ): Response {

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();

                $this->addFlash('success', [
                    'message' => 'security.reset_password.success',
                    'params' => []
                ]);
            } catch (\Exception $e) {
                /**
                 * Use a global error message in order to avoid giving too much information.
                 */
                $this->addFlash('error', [
                    'message' => 'security.global.error',
                    'params' => []
                ]);
            }

            /**
             * log the user automatically after resetting their password.
             * Use the 'form_login' authenticator name and the 'main' firewall name.
             * @see https://symfony.com/doc/current/security.html#login-programmatically
             */
//            $security->login($user, 'form_login', 'main');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form,
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

    #[Route('/upload-users', name: 'upload_users', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function uploadUsers(
        Request             $request,
        FileUploaderService $fileUploaderService,
        UserService         $userService
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
}
