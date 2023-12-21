<?php

declare(strict_types=1);

namespace App\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToDateIntervalTransformer implements DataTransformerInterface
{
    public function transform($value): array|\DateInterval|string|null
    {
        dump('Enter transform', $value);

        if (is_array($value)) {
            dump('if is_array', $value);
            $duration = new \DateInterval(sprintf('PT%dH%dM', $value['duration']['hours'], $value['duration']['minutes']));
            return $duration->format('%h hours %i minutes');
        }

        if (is_string($value)) {
            dump('if is_string', $value);
            $value = explode(' ', $value);

            /**
             * $newValue[0] = the number of hours coming from the string exploded.
             * $newValue[2] = the number of minutes.
             */
            //            $value = [
            //                'hours' => (int)$value[0],
            //                'minutes' => (int)$value[2],
            //            ];
            //            dump('transform into array when GET', $value);

            //            Topic[durationAsString][duration][hours]
            //            $value = [
            //                'duration' => [
            //                    'hours' => (int)$value[0],
            //                    'minutes' => (int)$value[2],
            //                ]
            //            ];
            //            $duration = [
            //                'duration' => new \DateInterval(sprintf('PT%dH%dM', (int)$value[0], (int)$value[2]))
            //            ];
            $duration = new \DateInterval(sprintf('PT%dH%dM', (int)$value[0], (int)$value[2]));
            $array = [$duration->h, $duration->i];
            dump('transform into DURATION array when GET', $duration, 'and return as array', $array);

            return $array;
            //            return ['duration' => [(int)$value[0], (int)$value[2]]];
            //            return new \DateInterval(sprintf('PT%dH%dM', (int)$value[0], (int)$value[2]));
        }

        dump('return null', $value);
        return null;
    }

    public function reverseTransform($value): array|string
    {
        dump('Enter reverseTransform', $value);
        //        array:3 [▼
        //  0 => 1
        //  1 => 15
        //  "duration" => DateInterval {#244 ▼
        //            interval: + 02:30:00.0
        //            +"y": 0
        //            +"m": 0
        //            +"d": 0
        //            +"h": 2
        //            +"i": 30
        //            +"s": 0
        //            +"f": 0.0
        //            +"invert": 0
        //            +"days": false
        //            +"from_string": false
        //  }
        //]
        if ($value['duration'] instanceof \DateInterval) {
            dump("if value[duration] \DateInterval", $value['duration']);
            return sprintf('%d hours %d minutes', $value['duration']->h, $value['duration']->i);
        }

        if ($value['duration'] instanceof \DateInterval) {
            dump(sprintf('if %s \DateInterval', $value[duration]), $value['duration']);
            return [
                'hours' => $value['duration']->h,
                'minutes' => $value['duration']->i,
            ];
            //            return $value['duration'];
        }

        return [];
    }
}
