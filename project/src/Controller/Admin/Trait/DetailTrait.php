<?php

namespace App\Controller\Admin\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

trait DetailTrait
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(Crud::PAGE_INDEX, Action::NEW, static function (Action $action) {
                return $action->setIcon('fa-solid fa-plus')->setLabel(false);
            })
            ->update(Crud::PAGE_NEW, Action::INDEX, static function (Action $action) {
                return $action->setIcon('fa-solid fa-list-ul')->setLabel(false);
            })
//            ->update(Crud::PAGE_NEW, Action::SHOW, function (Action $action) {
//                return $action->setIcon('fa-solid fa-list-ul')->setLabel(false);
//            })
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }
}
