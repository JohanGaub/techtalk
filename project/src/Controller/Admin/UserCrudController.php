<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
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
                    return $value ? $value->getLabel() : '';
                })
            ,
        ];
    }
}
