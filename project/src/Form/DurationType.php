<?php

declare(strict_types=1);

namespace App\Form;

use App\DataTransformer\ArrayToDateIntervalTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DurationType extends AbstractType
{
    public function __construct(private readonly ArrayToDateIntervalTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('hours', IntegerType::class, [
//                'label' => 'Hours',
//                'attr' => ['min' => 0, 'max' => 4],
//            ])
//            ->add('minutes', IntegerType::class, [
//                'label' => 'Minutes',
//                'attr' => ['min' => 0, 'max' => 45, 'step' => 15],
//            ]);

        ->add('duration', DateIntervalType::class, [
            'label' => false, // This avoids to display the label "Duration" twice. One coming from the EasyAdmin TextField and one from this form type.
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
        ]);

        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
