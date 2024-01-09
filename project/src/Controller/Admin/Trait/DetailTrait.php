<?php

namespace App\Controller\Admin\Trait;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

trait DetailTrait
{
    public const ACTION_INDEX_ICON = 'fa-solid fa-list-ul';

    public const ACTION_NEW_ICON = 'fa-solid fa-plus';

    public const ACTION_EDIT_ICON = 'fa-solid fa-pen-to-square';

    public const ACTION_SHOW_ICON = 'fa-solid fa-eye';

    public const ACTION_DELETE_ICON = 'fa-solid fa-trash';

    public const ACTION_SAVE_AND_RETURN_ICON = 'fa-solid fa-floppy-disk';

    public const ACTION_SAVE_AND_ADD_ANOTHER_ICON = 'fa-solid fa-arrow-rotate-right';

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            /**
             * In PAGE_INDEX :
             * - Add Action::DETAIL to show the detail page AND use an icon instead of a label
             * - For Action::EDIT, use an icon instead of a label
             * - For Action::DELETE, use an icon instead of a label
             * - For Action::NEW, use an icon instead of a label
             * - For Action::NEW, use an icon instead of a label
             * - For Action::NEW, use an icon instead of a label
             * - For BatchAction::DELETE, use an icon instead of a label
             */
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(
                Crud::PAGE_INDEX,
                Action::DETAIL,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_SHOW_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::EDIT,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_EDIT_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::DELETE,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_DELETE_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::NEW,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_NEW_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_INDEX,
                Action::BATCH_DELETE,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_DELETE_ICON)->setLabel(false)
            )
            /**
             * In PAGE_NEW :
             * - Add Action::INDEX to go back to the PAGE_INDEX
             * - For Action::INDEX, SAVE_AND_RETURN, SAVE_AND_ADD_ANOTHER use an icon instead of a label
             */
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(
                Crud::PAGE_NEW,
                Action::INDEX,
                static fn (Action $action) => $action->setIcon(self::ACTION_INDEX_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_NEW,
                Action::SAVE_AND_RETURN,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_SAVE_AND_RETURN_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_NEW,
                Action::SAVE_AND_ADD_ANOTHER,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_SAVE_AND_ADD_ANOTHER_ICON)->setLabel(false)
            )
            /**
             * In PAGE_EDIT :
             * - Add Action::INDEX to go back to the PAGE_INDEX
             * - For Action::INDEX, SAVE_AND_RETURN use an icon instead of a label
             */
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->update(
                Crud::PAGE_EDIT,
                Action::INDEX,
                static fn (Action $action) => $action->setIcon(self::ACTION_INDEX_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_EDIT,
                Action::SAVE_AND_RETURN,
                static fn (Action $action)
                => $action->setIcon(self::ACTION_SAVE_AND_RETURN_ICON)->setLabel(false)
            )
            /**
             * In PAGE_DETAIL :
             * - For Action::INDEX, EDIT, DELETE use an icon instead of a label
             */
            ->update(
                Crud::PAGE_DETAIL,
                Action::INDEX,
                static fn (Action $action) => $action->setIcon(self::ACTION_INDEX_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_DETAIL,
                Action::EDIT,
                static fn (Action $action) => $action->setIcon(self::ACTION_EDIT_ICON)->setLabel(false)
            )
            ->update(
                Crud::PAGE_DETAIL,
                Action::DELETE,
                static fn (Action $action) => $action->setIcon(self::ACTION_DELETE_ICON)->setLabel(false)
            )
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['updatedAt' => 'DESC']);
    }
}
