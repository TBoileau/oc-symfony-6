<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[Column(type: Types::TEXT)]
    #[Groups(['comment:read'])]
    #[NotBlank]
    private string $content;

    #[Column]
    #[Groups(['comment:read'])]
    private DateTimeImmutable $createdAt;

    #[ManyToOne]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['comment:read'])]
    private User $user;

    #[ManyToOne]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Trick $trick;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Comment
    {
        $this->user = $user;

        return $this;
    }

    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): Comment
    {
        $this->trick = $trick;

        return $this;
    }
}
