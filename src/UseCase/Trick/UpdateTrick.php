<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Image;
use App\Doctrine\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class UpdateTrick implements UpdateTrickInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $uploadsDir,
    ) {
    }

    public function __invoke(Trick $trick): void
    {
        /** @var ?UploadedFile $coverFile */
        $coverFile = $trick->getCoverFile();

        if (null !== $coverFile) {
            $trick->setCover(sprintf('%s.%s', Uuid::v4(), $coverFile->guessClientExtension()));
            $coverFile->move($this->uploadsDir, $trick->getCover());
        }

        foreach ($trick->getMedias() as $media) {
            if ($media instanceof Image) {
                $imageFile = $media->getFile();

                if (null !== $imageFile) {
                    $media->setFilename(sprintf('%s.%s', Uuid::v4(), $imageFile->guessClientExtension()));
                    $imageFile->move($this->uploadsDir, $media->getFilename());
                }
            }
        }

        $this->entityManager->flush();
    }
}
