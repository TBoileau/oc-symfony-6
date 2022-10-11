<?php

declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Doctrine\Entity\Media;
use App\Form\DataTransferObject\MediaDto;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements DataTransformerInterface<Media, MediaDto>
 */
final class MediaTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?MediaDto
    {
        if (!$value instanceof Media) {
            return null;
        }

        return MediaDto::fromEntity($value);
    }

    public function reverseTransform(mixed $value): ?Media
    {
        if (!$value instanceof MediaDto) {
            return null; // @codeCoverageIgnore
        }

        return $value->toEntity();
    }
}
