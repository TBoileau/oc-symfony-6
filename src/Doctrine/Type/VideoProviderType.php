<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\VideoProvider;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class VideoProviderType extends AbstractEnumType
{
    public const NAME = 'status';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return VideoProvider::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'varchar(10)';
    }
}
