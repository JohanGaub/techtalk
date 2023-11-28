<?php

namespace App\Service;

use App\Entity\Agency;
use App\Entity\User;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly string $userDirectory,
        private readonly EntityManagerInterface $entityManager,
        private readonly AgencyRepository $agencyRepository,
    ) {
    }

    public function addUsers($fileName): array {

        $openCsv = fopen(sprintf('%s/%s', $this->userDirectory, $fileName), "r");

        // Read and discard the first row which is the header
        fgetcsv($openCsv);

        $total = 0;
        $success = 0;
        $failure = 0;

        while (($data = fgetcsv($openCsv)) !== false) {

            try {
                $user = new User();
                $user->setEmail(uniqid('user_') . '@gmail.com');
//            $user->setEmail($data[0]); // use it when we want to use every role
                $user->setFirstName($data[1]);
                $user->setLastName($data[2]);
                $user->setRoles(explode(",", $data[3]));
                $user->setAgency($this->getAgencyById($data[4]));
                $user->setEnabled($data[5]);
                // Set a default password because when creating user it is needed even if we don't use it.
                // Indeed, we use the login link feature to connect to the website.
                $user->setPassword(password_hash('default_password', PASSWORD_BCRYPT));
                $this->entityManager->persist($user);
                ++$success;

            } catch (\Exception $e) {
                ++$failure;
            }

            ++$total;
        }

        fclose($openCsv);
        $this->entityManager->flush();

        return [
            'total' => $total,
            'success' => $success,
            'failure' => $failure,
        ];

        //5) Renvoyer l'information de réussite ou d'échec pour chaque utilisateur
        //6) Renvoyer le nombre d'utilisateurs ajoutés
        //7) Renvoyer le nombre d'utilisateurs échoués
        //8) Afficher ces informations dans une page twig
//        $users, $success, $failure, $total
    }

    private function getAgencyById($id): ?Agency {
        return $this->agencyRepository->find($id);
    }
}