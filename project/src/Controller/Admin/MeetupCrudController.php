<?php

namespace App\Controller\Admin;

use App\Entity\Meetup;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MeetupCrudController extends AbstractCrudController
{
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
        ];
    }
}
