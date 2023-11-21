<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginCheckType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('expires', HiddenType::class)
            ->add('user_email', HiddenType::class)
            ->add('hash', HiddenType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Continue',
            ]);
    }
}
