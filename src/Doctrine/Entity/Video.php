<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Entity]
class Video extends Media
{
    #[Column]
    #[NotBlank]
    private string $url;

    #[Column]
    private VideoProvider $provider;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getProvider(): VideoProvider
    {
        return $this->provider;
    }

    public function setProvider(VideoProvider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    #[Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->provider->check($this->url)) {
            $context->buildViolation('L\'url de la vidéo ne correspond pas au fournisseur.')
                ->atPath('url')
                ->addViolation();
        }
    }
}
