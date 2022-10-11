<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Doctrine\Entity\ResetPasswordRequest;
use App\Doctrine\Entity\User;
use App\Tests\WebTestCaseHelperTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

use function sprintf;

final class ResetPasswordTest extends WebTestCase
{
    use WebTestCaseHelperTrait;
    use MailerAssertionsTrait;

    public function testShouldResetPasswordAndRedirectToLogin(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $resetPasswordRequest = (new ResetPasswordRequest())
            ->setUser($user)
            ->setToken(Uuid::v4());

        $entityManager->persist($resetPasswordRequest);
        $entityManager->flush();

        $client->request(
            Request::METHOD_GET,
            sprintf('/reset-password/%s', $resetPasswordRequest->getToken())
        );
        $client->submitForm('Réinitialiser', self::createFormData());

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        /** @var ?ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $entityManager->getRepository(ResetPasswordRequest::class)->findOneBy(['user' => $user]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/login');
        self::assertNull($resetPasswordRequest);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $entityManager->refresh($user);

        self::assertTrue($passwordHasher->isPasswordValid($user, 'Password123!'));
    }

    public function testShouldRaiseHttpBadRequestDueToResetPasswordRequestExpired(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $resetPasswordRequest = (new ResetPasswordRequest())
            ->setUser($user)
            ->setToken(Uuid::v4());

        $reflectionProperty = new ReflectionProperty(ResetPasswordRequest::class, 'expiredAt');
        $reflectionProperty->setValue($resetPasswordRequest, new DateTimeImmutable('-1 day'));

        $entityManager->persist($resetPasswordRequest);
        $entityManager->flush();

        $client->request(
            Request::METHOD_GET,
            sprintf('/reset-password/%s', $resetPasswordRequest->getToken())
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldRaiseHttpNotFoundExceptionDueToInvalidToken(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reset-password/fail');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{
     *      'reset_password[plainPassword]': string
     * } $formData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $resetPasswordRequest = (new ResetPasswordRequest())
            ->setUser($user)
            ->setToken(Uuid::v4());

        $entityManager->persist($resetPasswordRequest);
        $entityManager->flush();

        $client->request(
            Request::METHOD_GET,
            sprintf('/reset-password/%s', $resetPasswordRequest->getToken())
        );
        $client->submitForm('Réinitialiser', $formData);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{
     *      'reset_password[plainPassword]': string
     * }>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'blank plainPassword' => [self::createFormData(plainPassword: '')];
        yield 'invalid plainPassword' => [self::createFormData(plainPassword: 'fail')];
    }

    /**
     * @return array{
     *      'reset_password[plainPassword]': string
     * }
     */
    private static function createFormData(string $plainPassword = 'Password123!'): array
    {
        return ['reset_password[plainPassword]' => $plainPassword];
    }
}
