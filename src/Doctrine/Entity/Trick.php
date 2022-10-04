<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\TrickRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;

#[Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column]
    #[Groups(['trick:read'])]
    private string $name;

    #[Column(type: Types::TEXT)]
    private string $description;

    #[Column(unique: true)]
    #[Groups(['trick:read'])]
    private string $slug;

    #[Column]
    #[Groups(['trick:read'])]
    private DateTimeImmutable $createdAt;

    #[Column]
    private DateTimeImmutable $updatedAt;

    #[ManyToOne]
    #[JoinColumn(onDelete: 'SET NULL')]
    #[Groups(['trick:read'])]
    private ?User $user = null;

    #[Column]
    #[Groups(['trick:read'])]
    private string $cover;

    #[ManyToOne]
    #[JoinColumn(nullable: false)]
    #[Groups(['trick:read'])]
    private Category $category;

    /**
     * @var Collection<int, Media>
     */
    #[OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $medias;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Trick
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Trick
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Trick
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): Trick
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Trick
    {
        $this->user = $user;

        return $this;
    }

    public function getCover(): string
    {
        return $this->cover;
    }

    public function setCover(string $cover): Trick
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        $media->setTrick($this);
        $this->medias->add($media);

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        $media->setTrick(null);
        $this->medias->removeElement($media);

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): Trick
    {
        $this->category = $category;

        return $this;
    }
}
