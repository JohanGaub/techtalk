<?php

declare(strict_types=1);

namespace App\Enum;

enum CurrentPlace: string
{
    use EnumTrait;

    case DRAFT = 'draft';
    case IN_REVIEW = 'in_review';
    case PUBLISHED = 'published';
}
