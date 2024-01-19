<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Topic;
use App\Enum\DurationCategory;
use App\Field\DateIntervalField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

class TopicCrudController extends AbstractCommonCrudController
{
    public static function getEntityFqcn(): string
    {
        return Topic::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextareaField::new('description'),
            DateIntervalField::new('duration', 'Duration')
                ->setFormType(DateIntervalType::class)
                ->setFormTypeOptions([
                    'label' => false,
                    'widget' => 'choice',
                    'with_years' => false,
                    'with_months' => false,
                    'with_days' => false,
                    'with_minutes' => true,
                    'with_hours' => true,
                    'minutes' => array_combine(range(0, 45, 15), range(0, 45, 15)),
                    'hours' => range(0, 4),
                    'placeholder' => [
                        'hours' => 'Hours',
                        'minutes' => 'Minutes'
                    ],
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
                ->formatValue(static fn ($value) =>  $value ? $value->getName() : ''),
            AssociationField::new('userProposer')
                ->autocomplete()
                ->formatValue(static fn ($value) =>  $value ? $value->getEmail() : ''),
            AssociationField::new('userPresenter')
                ->autocomplete()
                ->formatValue(static fn ($value) =>  $value ? $value->getEmail() : ''),
            AssociationField::new('userPublisher')
                ->autocomplete()
                ->formatValue(static fn ($value) =>  $value ? $value->getEmail() : '')
                ->hideOnForm(),
            DateTimeField::new('inReviewAt')->hideOnForm(),
            DateTimeField::new('publishedAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
            /**
             * In Page::NEW and Page::EDIT, it enables to select users voting to this specific topic.
             * In Page::INDEX, it displays the number of users' votes for any specific topic.
             */
            AssociationField::new('users', "Number of votes")
                ->autocomplete(),
            /**
             * In Page::DETAIL, it displays all users' votes for the selected topic.
             */
            ArrayField::new('users', "Users' votes")->onlyOnDetail(),
        ];
    }
}
