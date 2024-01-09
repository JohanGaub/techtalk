<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

readonly class LoginLinkService
{
    public function __construct(
        private NotifierInterface         $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function sendLoginLink(string $postedEmail): void
    {
        $user = $this->userRepository->findOneBy(['email' => $postedEmail]);

        if (null === $user) {
            throw new UserNotFoundException('User not found');
        }

        $userEmail = $user->getEmail();

        try {
            // create a login link for $user this returns an instance of LoginLinkDetails
            $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

            // create a notification based on the login link details
            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Link to connect to Techtalk website!' // email subject
            );

            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $this->notifier->send($notification, $recipient);
        } catch (ExpiredLoginLinkException|TransportExceptionInterface $e) {
            // If the link is expired or has a transport exception, log the error.
            $this->logger->error(sprintf('Failed to send login link to %s: %s', $userEmail, $e->getMessage()));
        }
    }
}
