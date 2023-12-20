<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\DetailTrait;
use App\Entity\Meetup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetupCrudController extends AbstractCrudController
{
    use DetailTrait;

    public static function getEntityFqcn(): string
    {
        return Meetup::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('label'),
            TextareaField::new('description'),
            DateField::new('startDate'),
            DateField::new('endDate'),
            IntegerField::new('capacity'),
            AssociationField::new('agency')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getLabel() : '';
                }),
            AssociationField::new('userOrganiser')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getEmail() : '';
                }),
            /**
             * It displays the number of topics for each meetup in the index page.
             */
            AssociationField::new('topics')->onlyOnIndex(),
            /**
             * It displays all topics made in meetup's detail page.
             */
            ArrayField::new('topics')->onlyOnDetail(),
        ];
    }
}
