<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Trait\DetailTrait;
use App\Entity\Topic;
use App\Enum\DurationCategory;
use App\Form\DurationType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TopicCrudController extends AbstractCrudController
{
    use DetailTrait;

    public static function getEntityFqcn(): string
    {
        return Topic::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->setSortable(true),
            TextField::new('label'),
            TextareaField::new('description'),

            /**
             * For duration field, we use a custom form type (@see DurationType) with DateInterval object in inputs.
             * And we use a custom data transformer (@see ArrayToDateIntervalTransformer) to transform the hours and minutes into suitable format.
             */
            TextField::new('durationAsString', 'Duration')
                ->setFormType(DurationType::class)
                ->setFormTypeOptions([
                    'label' => false,
                ])
            ,
            ChoiceField::new('durationCategory')->setChoices(DurationCategory::cases()),
            ChoiceField::new('currentPlace')->setChoices([
                'Draft' => 'draft',
                'In review' => 'in_review',
                'Published' => 'published',
            ]),
            AssociationField::new('meetup')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getLabel() : '';
                }),
            AssociationField::new('userProposer')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getEmail() : '';
                }),
            AssociationField::new('userPresenter')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getEmail() : '';
                }),
            AssociationField::new('userPublisher')
                ->autocomplete()
                ->formatValue(static function ($value) {
                    return $value ? $value->getEmail() : '';
                })->hideOnForm(),
            DateTimeField::new('inReviewAt')->hideOnForm(),
            DateTimeField::new('publishedAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}
