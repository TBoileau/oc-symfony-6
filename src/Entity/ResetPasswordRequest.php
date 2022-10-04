<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\NotNull;

#[Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private ?int $id = null;

    #[NotNull]
    #[ManyToOne]
    #[JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[Column]
    private DateTimeImmutable $expiredAt;

    #[Column]
    private Uuid $token;

    public function __construct()
    {
        $this->expiredAt = (new DateTimeImmutable())->modify('+1 hour');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }

    public function setToken(Uuid $token): self
    {
        $this->token = $token;

        return $this;
    }
}
