<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Topic;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\SecurityBundle\Security;

class EditTopicType extends NewTopicType
{
    public function __construct(private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if ($this->security->isGranted('ROLE_BOARD_USER')) {
            $builder
                ->add('currentPlace')
                ->add('presentedAt', DateType::class, [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'html5' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
