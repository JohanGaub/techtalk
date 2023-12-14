<?php

namespace App\Enum;

enum DurationCategory: string
{
    use EnumTrait;

    case Short = 'short';
    case Medium = 'medium';
    case Long = 'long';
}
