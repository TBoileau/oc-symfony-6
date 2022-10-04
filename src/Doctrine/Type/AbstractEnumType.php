<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

use function enum_exists;
use function is_int;
use function is_string;

abstract class AbstractEnumType extends Type
{
    /**
     * @return class-string<BackedEnum>
     */
    abstract public static function getEnumsClass(): string;

    abstract public function getSQLDeclaration(array $column, AbstractPlatform $platform): string;

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return null;
    }

    /**
     * @param mixed $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (false === enum_exists(static::getEnumsClass())) {
            throw new \LogicException('This class should be an enum');
        }

        if (null === $value) {
            return null;
        }

        if (!(is_int($value) || is_string($value))) {
            throw new \LogicException('Value should be int or string');
        }

        /** @phpstan-ignore-next-line */
        return static::getEnumsClass()::tryFrom($value);
    }
}