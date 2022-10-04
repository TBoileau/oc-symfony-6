<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class Video extends Media
{
    #[Column]
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
}
