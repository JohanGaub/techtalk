<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class UserFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('users', FileType::class, [
                'label' => 'Select users file you want to upload',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/csv',
                            'text/csv',
                            'application/xlsx',
                            'application/xls',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV or Excel file',
                    ])
                ],
            ])
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Upload',
                ]
            );
    }
}
