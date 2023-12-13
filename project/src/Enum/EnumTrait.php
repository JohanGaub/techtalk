<?php

declare(strict_types=1);

namespace App\Enum;

trait EnumTrait
{
    /**
     * @see https://www.php.net/manual/fr/language.enumerations.examples.php
     * self:cases() is an array of Enum instances.
     * Each Enum instance enter the callback function.
     * And then, array_map() returns an array of Enum values.
     */
    public static function values(): array
    {
        return array_map(static fn ($enum) => $enum->value, self::cases());
    }

    public static function names(): array
    {
        return array_map(static fn ($enum) => $enum->name, self::cases());
    }

    //    /**
    //     * @throws \ReflectionException
    //     */
    //    public static function makeEnumByCase(string $name): ?static {
    //        $reflection = new \ReflectionEnum(self::class);
    //        /** @phpstan-ignore-next-line */
    //        if ($reflection->hasCase($name)) {
    //            /** @phpstan-ignore-next-line */
    //            return $reflection->getCase($name)->getValue();
    //        }
    //
    //        return null;
    //    }
    //
    //    public static function values(): array {
    //        return array_column(self::cases(), 'value');
    //    }
    //
    //    public static function names(): array {
    //        return array_column(self::cases(), 'name');
    //    }
}
