<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Meetup;
use App\Entity\Topic;
use App\Entity\User;
use App\Enum\CurrentPlace;
use App\Enum\DurationCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label')
            ->add('description')
            ->add('duration', DateIntervalType::class, [
                'widget' => 'choice',
                'with_years' => false,
                'with_months' => false,
                'with_days' => false,
                'with_minutes' => true,
                'with_hours' => true,
                'placeholder' => [
                    'hours' => 'Hours',
                    'minutes' => 'Minutes'
                ],
                'minutes' => array_combine(range(0, 45, 15), range(0, 45, 15)),
                'hours' => range(0, 4),
            ])
            ->add('durationCategory', EnumType::class, [
                'placeholder' => 'Select a duration category',
                'class' => DurationCategory::class,
            ])
            ->add('userPresenter', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'required' => false,
                'placeholder' => 'Select a user',
            ])
            ->add('meetup', EntityType::class, [
                'class' => Meetup::class,
                'choice_label' => 'label',
                'required' => false,
                'placeholder' => 'Select a meetup',
            ]);


        if ($this->security->isGranted('ROLE_BOARD_USER')) {
            $builder->add('currentPlace', ChoiceType::class, [
                'placeholder' => 'Select a current place',
                'choices' => [
                    CurrentPlace::DRAFT->value => CurrentPlace::DRAFT->value,
                    CurrentPlace::IN_REVIEW->value => CurrentPlace::IN_REVIEW->value,
                    CurrentPlace::PUBLISHED->value => CurrentPlace::PUBLISHED->value,
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
