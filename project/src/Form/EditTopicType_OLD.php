<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Topic;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditTopicTypeOLD extends TopicType
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
