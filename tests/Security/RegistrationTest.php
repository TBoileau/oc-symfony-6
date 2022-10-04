<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationTest extends WebTestCase
{
    use WebTestCaseHelperTrait;
    use MailerAssertionsTrait;

    public function testShouldRegisterUserAndRedirectToIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/register');
        $client->submitForm('S\'inscrire', self::createFormData());

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        /** @var ?User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user@email.com']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/');
        self::assertNotNull($user);
        self::assertTrue($passwordHasher->isPasswordValid($user, 'Password123!'));
        self::assertSame('user', $user->getNickname());
        self::assertSame('user@email.com', $user->getEmail());
        self::assertNotNull($user->getRegistrationToken());
        self::assertEmailCount(1);

        /** @var RawMessage $email */
        $email = self::getMailerMessage();

        self::assertEmailHtmlBodyContains($email, (string) $user->getRegistrationToken());
        self::assertEmailAddressContains($email, 'To', 'user@email.com');
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{
     *      'registration[email]': string,
     *      'registration[nickname]': string,
     *      'registration[plainPassword]': string
     * } $formData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/register');
        $client->submitForm('S\'inscrire', $formData);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{
     *      'registration[email]': string,
     *      'registration[nickname]': string,
     *      'registration[plainPassword]': string
     * }>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'blank email' => [self::createFormData(email: '')];
        yield 'invalid email' => [self::createFormData(email: 'fail')];
        yield 'used email' => [self::createFormData(email: 'user+1@email.com')];
        yield 'blank nickname' => [self::createFormData(nickname: '')];
        yield 'blank plainPassword' => [self::createFormData(plainPassword: '')];
        yield 'invalid plainPassword' => [self::createFormData(plainPassword: 'fail')];
    }

    /**
     * @return array{
     *      'registration[email]': string,
     *      'registration[nickname]': string,
     *      'registration[plainPassword]': string
     * }
     */
    private static function createFormData(
        string $email = 'user@email.com',
        string $nickname = 'user',
        string $plainPassword = 'Password123!'
    ): array {
        return [
            'registration[email]' => $email,
            'registration[nickname]' => $nickname,
            'registration[plainPassword]' => $plainPassword,
        ];
    }
}
