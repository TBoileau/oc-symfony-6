<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Doctrine\Entity\Image;
use App\Doctrine\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

final class CreateTrick implements CreateTrickInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $uploadsDir,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function __invoke(Trick $trick): void
    {
        $trick->setSlug((string) $this->slugger->slug($trick->getName()));

        /** @var UploadedFile $coverFile */
        $coverFile = $trick->getCoverFile();
        $trick->setCover(sprintf('%s.%s', Uuid::v4(), $coverFile->guessClientExtension()));
        $coverFile->move($this->uploadsDir, $trick->getCover());

        foreach ($trick->getMedias() as $media) {
            if ($media instanceof Image) {
                /** @var UploadedFile $imageFile */
                $imageFile = $media->getFile();
                $media->setFilename(sprintf('%s.%s', Uuid::v4(), $imageFile->guessClientExtension()));
                $imageFile->move($this->uploadsDir, $media->getFilename());
            }
        }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }
}
