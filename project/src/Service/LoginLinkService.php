<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use Psr\Log\LogLevel;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

readonly class LoginLinkService
{
    public function __construct(
        private NotifierInterface         $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private UserRepository $userRepository,
        private LoggerService    $loggerService
    ) {
    }

    public function sendLoginLink(string $postedEmail): void
    {
        if (!$this->validateEmail($postedEmail)) {
            $this->loggerService->log(
                LogLevel::ERROR,
                'Invalid email: %s.'
            );
            return;
        }

        try {
            $user = $this->getUser($postedEmail);
            $loginLinkDetails = $this->generateLoginLink($user);

            $this->notifyUser($loginLinkDetails, $user);
        } catch (UserNotFoundException $userNotFoundException) {
            // If user not found, log the error.
            $this->loggerService->log(
                LogLevel::ERROR,
                'User with posted email %s not found.',
                [$postedEmail],
                $userNotFoundException
            );

        } catch (ExpiredLoginLinkException|TransportExceptionInterface $exception) {
            // If the link is expired or has a transport exception, log the error.
            $this->loggerService->log(
                LogLevel::ERROR,
                'Failed to send login link to %s.',
                [$postedEmail],
                $exception
            );
        } catch (\Exception $exception) {
            // Catch any other exceptions that might arise and log them.
            $this->loggerService->log(
                LogLevel::ERROR,
                'An unexpected error occurred while sending login link to %s.',
                [$postedEmail],
                $exception
            );
        }
    }

    private function validateEmail(string $email): bool
    {
        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

        if ($isValid) {
            [, $domain] = explode('@', $email);
            $isValid = checkdnsrr($domain, 'MX');
        }

        return $isValid;
    }

    private function getUser(string $postedEmail): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $postedEmail]);
        if (null === $user) {
            throw new UserNotFoundException('User not found.');
        }

        return $user;
    }

    private function generateLoginLink(UserInterface $user): LoginLinkDetails
    {
        return $this->loginLinkHandler->createLoginLink($user);
    }

    private function notifyUser(LoginLinkDetails $loginLinkDetails, UserInterface $user): void
    {
        $notification = $this->createNotification($loginLinkDetails);
        $recipient = $this->createRecipient($user);
        $this->notifier->send($notification, $recipient);
    }

    private function createNotification(LoginLinkDetails $loginLinkDetails): LoginLinkNotification
    {
        return new LoginLinkNotification(
            $loginLinkDetails,
            'Link to connect to Techtalk website!'
        );
    }

    private function createRecipient(UserInterface $user): Recipient
    {
        if (!method_exists($user, 'getEmail')) {
            throw new \BadMethodCallException('Passed user object does not have a getEmail method.');
        }

        return new Recipient($user->getEmail());
    }
}
