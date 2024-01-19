<?php

namespace App\Controller\Admin;

use App\Entity\Agency;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AgencyCrudControllerAbstract extends AbstractCommonCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agency::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            /**
             * It displays the number of meetups for each agency in the index page.
             */
            AssociationField::new('meetups')->onlyOnIndex(),
            /**
             * It displays all meetups made in agency's detail page.
             */
            ArrayField::new('meetups')->onlyOnDetail(),
        ];
    }
}
