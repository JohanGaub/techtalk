<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Agency;
use App\Entity\User;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

readonly class UserService
{
    public function __construct(
        private string                 $userDirectory,
        private EntityManagerInterface $entityManager,
        private AgencyRepository       $agencyRepository,
        private LoggerService    $loggerService
    ) {
    }

    public function addUsers($fileName): array
    {
        $filePath = sprintf('%s/%s', $this->userDirectory, $fileName);
        $openCsv = fopen($filePath, "r");

        if ($openCsv === false) {
            throw new FileException(sprintf('Failed to open file: %s.', $filePath));
        }

        // Read and discard the first row which is the header
        fgetcsv($openCsv);

        $total = 0;
        $success = 0;
        $failure = 0;

        while (($data = fgetcsv($openCsv)) !== false) {
            $user = null;

            try {
                $user = $this->createUser($data);
                $this->entityManager->persist($user);
                ++$success;

                $this->loggerService->log(
                    LogLevel::INFO,
                    'User with email %s added successfully.',
                    [$user->getEmail()]
                );
            } catch (\Exception $exception) {
                ++$failure;

                $email = $user instanceof User ? $user->getEmail() : 'Unknown';
                $this->loggerService->log(
                    LogLevel::ERROR,
                    'Error while adding user with email %s.',
                    [$email],
                    $exception
                );
            }

            ++$total;
        }

        fclose($openCsv);
        $this->entityManager->flush();

        $this->loggerService->log(
            LogLevel::INFO,
            'Added %d users, with %d failures.',
            [$total, $failure]
        );

        return [
            'total' => $total,
            'success' => $success,
            'failure' => $failure,
        ];
    }

    private function createUser(array $data): User
    {
        $user = new User();
        $user->setEmail(uniqid('user_') . '@gmail.com');
        //            $user->setEmail($data[0]); // use it when we want to use every role
        $user->setFirstName($data[1]);
        $user->setLastName($data[2]);
        $user->setRoles(explode(",", $data[3]));
        $user->setAgency($this->getAgencyById($data[4]));
        $user->setEnabled((bool) $data[5]);
        /**
         * Set a default password because when creating user it is needed even if we don't use it.
         * Indeed, we use the login link feature to connect to the website.
         */
        $user->setPassword(password_hash('default_password', PASSWORD_BCRYPT));

        return $user;
    }

    private function getAgencyById($id): ?Agency
    {
        return $this->agencyRepository->find($id);
    }
}
