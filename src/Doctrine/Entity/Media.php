<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap(['image' => Image::class, 'video' => Video::class])]
abstract class Media
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    protected ?int $id = null;

    #[ManyToOne(inversedBy: 'medias')]
    protected ?Trick $trick = null;

    abstract public function type(): string;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
