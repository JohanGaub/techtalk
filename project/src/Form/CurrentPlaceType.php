<?php

declare(strict_types=1);

namespace App\Form;

use App\Enum\CurrentPlace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrentPlaceType extends AbstractType
{
    private const BUTTON_PRIMARY = 'btn btn-primary';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('draft', ButtonType::class, [
                'attr' => ['class' => self::BUTTON_PRIMARY],
            ])
            ->add('in_review', ButtonType::class, [
                'attr' => ['class' => self::BUTTON_PRIMARY],
            ])
            ->add('publish', ButtonType::class, [
                'attr' => ['class' => self::BUTTON_PRIMARY],
            ]);
        //            ->add('currentPlace', ButtonType::class, [
        //                'placeholder' => 'Select a current place',
        //                'choices' => [
        //                    CurrentPlace::DRAFT->value => CurrentPlace::DRAFT->value,
        //                    CurrentPlace::IN_REVIEW->value => CurrentPlace::IN_REVIEW->value,
        //                    CurrentPlace::PUBLISHED->value => CurrentPlace::PUBLISHED->value,
        //                ],
        //            ])
        //        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
