<?php

declare(strict_types=1);

namespace App\Tests\Profile;

use App\Doctrine\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UpdatePasswordTest extends WebTestCase
{
    public function testShouldUpdatePassword(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/profile/update-password');
        $client->submitForm('Modifier', self::createFormData());

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/profile/update-password');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $entityManager->refresh($user);

        self::assertTrue($passwordHasher->isPasswordValid($user, 'Password123!'));
    }

    public function testShouldRaiseHttpAccessDeniedExceptionAndRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/profile/update-password');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('http://localhost/login');
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{
     *      'update_password[currentPassword]': string,
     *      'update_password[plainPassword]': string
     * } $formData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/profile/update-password');
        $client->submitForm('Modifier', $formData);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{
     *      'update_password[currentPassword]': string,
     *      'update_password[plainPassword]': string
     * }>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'blank plainPassword' => [self::createFormData(plainPassword: '')];
        yield 'invalid plainPassword' => [self::createFormData(plainPassword: 'fail')];
        yield 'blank currentPassword' => [self::createFormData(currentPassword: '')];
        yield 'wrong currentPassword' => [self::createFormData(currentPassword: 'fail')];
    }

    /**
     * @return array{
     *      'update_password[currentPassword]': string,
     *      'update_password[plainPassword]': string
     * }
     */
    private static function createFormData(
        string $currentPassword = 'password',
        string $plainPassword = 'Password123!'
    ): array {
        return [
            'update_password[currentPassword]' => $currentPassword,
            'update_password[plainPassword]' => $plainPassword,
        ];
    }
}
