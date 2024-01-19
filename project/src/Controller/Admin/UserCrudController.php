<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Service\LoginLinkService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserCrudController extends AbstractCommonCrudController
{
    public function __construct(private readonly LoginLinkService $loginLinkService)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('email'),
            TextField::new('password')->hideOnIndex(),
            ChoiceField::new('roles')->allowMultipleChoices()->setChoices([
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
                'Board User' => 'ROLE_BOARD_USER',
            ]),
            ChoiceField::new('enabled')->renderAsBadges()->setChoices([
                'Yes' => 1,
                'No' => 0,
            ]),
            AssociationField::new('agency')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getName() : '';
                })
            ,
            AssociationField::new('proposedTopics')->onlyOnIndex(),
            ArrayField::new('proposedTopics')->onlyOnDetail(),
            /**
             * It displays the number of presentedTopics by each user in the index page.
             */
            AssociationField::new('presentedTopics')->onlyOnIndex(),

            /**
             * It displays all presentedTopics by the user in its detail page.
             */
            ArrayField::new('presentedTopics')->onlyOnDetail(),
            AssociationField::new('organisedMeetups')->onlyOnIndex(),
            ArrayField::new('organisedMeetups')->onlyOnDetail(),
        ];
    }

    /**
     * Batch action used in configureActions() method to send login link to users.
     */
    public function sendLoginLinkToUsers(BatchActionDto $batchActionDto): RedirectResponse
    {
        $className = $batchActionDto->getEntityFqcn();
        $entityManager = $this->container->get('doctrine')->getManagerForClass($className);

        foreach ($batchActionDto->getEntityIds() as $id) {
            $user = $entityManager->find($className, $id);
            $this->loginLinkService->sendLoginLink($user->getEmail());
        }

        $this->addFlash('success', [
            'message' => 'Login links sent successfully.',
            'params' => []
        ]);

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    public function configureActions(Actions $actions): Actions
    {
        // Call the configureActions() method of the parent class from the DetailTrait.
//        $actions = $this->parentConfigureActions($actions);

        $sendLoginLinkToUsers = Action::new('sendLoginLinkToUsers', 'Send Login Link to users')
            ->linkToCrudAction('sendLoginLinkToUsers')
            ->addCssClass('btn btn-primary')
            ->setIcon('fa-solid fa-handshake');

        return $actions
            ->addBatchAction($sendLoginLinkToUsers);
    }


}
