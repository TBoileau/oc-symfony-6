<?php

declare(strict_types=1);

namespace App\Tests\Profile;

use App\Doctrine\Entity\User;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateAvatarTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldUpdateAvatar(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/profile/update-avatar');
        $client->submitForm('Modifier', self::createFormData(self::fakeImage()));

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/profile/update-avatar');

        $entityManager->refresh($user);

        self::assertNotNull($user->getAvatar());

        $avatar = $user->getAvatar();

        $client->followRedirect();

        $client->submitForm('Modifier', self::createFormData(self::fakeImage()));

        $entityManager->refresh($user);

        self::assertNotNull($user->getAvatar());
        self::assertNotSame($avatar, $user->getAvatar());
    }

    public function testShouldRaiseHttpAccessDeniedExceptionAndRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/profile/update-avatar');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('http://localhost/login');
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{'update_avatar[avatarFile]': ?UploadedFile} $formData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/profile/update-avatar');
        $client->submitForm('Modifier', $formData);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{'update_avatar[avatarFile]': ?UploadedFile}>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'avatarFile null' => [self::createFormData()];
        yield 'invalid avatarFile' => [self::createFormData(self::fakeFile())];
    }

    /**
     * @return array{'update_avatar[avatarFile]': ?UploadedFile}
     */
    private static function createFormData(?UploadedFile $avatarFile = null): array
    {
        return [
            'update_avatar[avatarFile]' => $avatarFile,
        ];
    }
}
