<?php

declare(strict_types=1);

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToDateIntervalTransformer implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        if ($value instanceof \DateInterval) {
            return [
                'hours' => $value->h,
                'minutes' => $value->i,
            ];
        }

        return [];
    }

    public function reverseTransform($value): ?\DateInterval
    {
        if (is_array($value)) {
            return new \DateInterval(sprintf('PT%dH%dM', $value['hours'], $value['minutes']));
        }

        return null;
    }
}
