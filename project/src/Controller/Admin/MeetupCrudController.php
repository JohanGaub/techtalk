<?php

namespace App\Controller\Admin;

use App\Entity\Meetup;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetupCrudController extends AbstractCommonCrudController
{
    public static function getEntityFqcn(): string
    {
        return Meetup::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextareaField::new('description'),
            DateField::new('startDate'),
            DateField::new('endDate'),
            IntegerField::new('capacity'),
            AssociationField::new('agency')
                ->autocomplete()
                ->formatValue(static fn ($value) =>  $value ? $value->getName() : ''),
            AssociationField::new('userOrganiser')
                ->autocomplete()
                ->formatValue(static fn ($value) =>  $value ? $value->getEmail() : ''),
            /**
             * It displays the number of topics for each meetup in the index page.
             */
            AssociationField::new('topics')->onlyOnIndex(),
            /**
             * It displays all topics made in meetup's detail page.
             */
            ArrayField::new('topics')->onlyOnDetail(),
            /**
             * In Page::NEW and Page::EDIT, it enables to select user participants to this specific meetup.
             * In Page::INDEX, it displays the number of participants for any specific meetup.
             */
            AssociationField::new('users', 'Participants')
                ->autocomplete(),
            /**
             * In Page::DETAIL, it displays all participants for the selected meetup.
             */
            ArrayField::new('users', 'Participants')->onlyOnDetail(),
        ];
    }
}
