<?php

declare(strict_types=1);

namespace App\Form\DataTransferObject;

use App\Doctrine\Entity\Image;
use App\Doctrine\Entity\Media;
use App\Doctrine\Entity\Video;
use App\Doctrine\Entity\VideoProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

final class MediaDto
{
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';

    #[NotBlank]
    #[Choice(choices: [self::TYPE_IMAGE, self::TYPE_VIDEO])]
    public string $type;

    public ?string $filename = null;

    public ?UploadedFile $file = null;

    public ?string $alt = null;

    public ?string $url = null;

    public ?VideoProvider $provider = null;

    private static function create(
        string $type,
        ?string $filename = null,
        ?UploadedFile $file = null,
        ?string $alt = null,
        ?string $url = null,
        ?VideoProvider $provider = null
    ): MediaDto {
        $media = new self();
        $media->type = $type;
        $media->filename = $filename;
        $media->file = $file;
        $media->alt = $alt;
        $media->url = $url;
        $media->provider = $provider;

        return $media;
    }

    public static function fromEntity(Media $media): self
    {
        /* @phpstan-ignore-next-line */
        return match ($media::class) {
            Image::class => self::create(
                type: self::TYPE_IMAGE,
                filename: $media->getFilename(),
                alt: $media->getAlt()
            ),
            Video::class => self::create(
                type: self::TYPE_VIDEO,
                url: $media->getUrl(),
                provider: $media->getProvider()
            ),
        };
    }

    public function toEntity(): Media
    {
        if (self::TYPE_IMAGE === $this->type) {
            $image = new Image();

            /** @var UploadedFile $file */
            $file = $this->file;
            $image->setFile($file);

            /** @var string $alt */
            $alt = $this->alt;
            $image->setAlt($alt);

            return $image;
        }

        $video = new Video();

        /** @var string $url */
        $url = $this->url;
        $video->setUrl($url);

        /** @var VideoProvider $provider */
        $provider = $this->provider;
        $video->setProvider($provider);

        return $video;
    }
}
