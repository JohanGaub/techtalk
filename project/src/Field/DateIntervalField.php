<?php

declare(strict_types=1);

namespace App\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DateIntervalField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): DateIntervalField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            // this template is used to render the field in 'detail' and 'index' pages
            ->setTemplatePath('admin/field/dateinterval.html.twig')
            // this is used to render the field in 'edit' and 'new' pages
            ->setFormType(TextField::class)
            ->addCssClass('field-dateinterval');
    }
}
