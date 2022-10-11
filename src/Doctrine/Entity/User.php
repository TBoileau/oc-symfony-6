<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

use App\Doctrine\Repository\UserRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

#[Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
#[UniqueEntity(fields: ['nickname'], message: 'Ce pseudo est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    #[Groups(['trick:read'])]
    private ?int $id = null;

    #[NotBlank]
    #[Email]
    #[Column(length: 180, unique: true)]
    private string $email;

    #[Column]
    private string $password;

    #[NotBlank(groups: ['password'])]
    #[Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        htmlPattern: '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$',
        groups: ['password']
    )]
    private ?string $plainPassword = null;

    #[NotBlank]
    #[Column(unique: true)]
    #[Groups(['trick:read', 'comment:read'])]
    private string $nickname;

    #[Column(type: 'uuid', nullable: true)]
    private ?Uuid $registrationToken = null;

    #[Column(nullable: true)]
    #[Groups(['comment:read'])]
    private ?string $avatar = null;

    #[NotNull(groups: ['avatar'])]
    #[ImageConstraint(groups: ['avatar'])]
    private ?UploadedFile $avatarFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getRegistrationToken(): ?Uuid
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?Uuid $registrationToken): void
    {
        $this->registrationToken = $registrationToken;
    }

    public function hasValidatedRegistration(): bool
    {
        return null === $this->registrationToken;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): User
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatarFile(): ?UploadedFile
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?UploadedFile $avatarFile): User
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    /**
     * @return array{
     *      id: ?int,
     *      email: string,
     *      password: string,
     *      nickname: string,
     *      avatar: ?string,
     *      registrationToken: ?string
     * }
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'nickname' => $this->nickname,
            'avatar' => $this->avatar,
            'registrationToken' => null !== $this->registrationToken ? (string) $this->registrationToken : null,
        ];
    }

    /**
     * @param array{
     *      id: ?int,
     *      email: string,
     *      password: string,
     *      nickname: string,
     *      avatar: ?string,
     *      registrationToken: ?string
     * } $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->nickname = $data['nickname'];
        $this->avatar = $data['avatar'];
        $this->registrationToken = null !== $data['registrationToken']
            ? Uuid::fromString($data['registrationToken'])
            : null;
    }
}
