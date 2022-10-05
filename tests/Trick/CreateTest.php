<?php

declare(strict_types=1);

namespace App\Tests\Trick;

use App\Doctrine\Entity\User;
use App\Doctrine\Entity\VideoProvider;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldCreateTrickAndRedirectToShowPage(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, '/create');
        self::assertResponseIsSuccessful();

        /** @@phpstan-ignore-next-line */
        $token = $crawler
            ->filter('form[name=trick]')
            ->form()
            ->get('trick')['_token']
            ->getValue();

        $formData = self::createFormData(
            coverFile: self::fakeImage(),
            medias: [
                [
                    'type' => 'video',
                    'provider' => VideoProvider::Youtube->value,
                    'url' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
                ],
                [
                    'type' => 'image',
                    'alt' => 'image',
                    'file' => self::fakeImage(),
                ],
            ]
        );

        $formData['parameters']['trick']['_token'] = $token;

        $client->request(Request::METHOD_POST, '/create', $formData['parameters'], $formData['files']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testShouldRaiseHttpAccessDeniedAndRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/create');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('http://localhost/login');
    }

    /**
     * @dataProvider provideInvalidFormData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, '/create');
        self::assertResponseIsSuccessful();

        /** @@phpstan-ignore-next-line */
        $token = $crawler
            ->filter('form[name=trick]')
            ->form()
            ->get('trick')['_token']
            ->getValue();

        $formData['parameters']['trick']['_token'] = $token;

        $client->request(Request::METHOD_POST, '/create', $formData['parameters'], $formData['files']);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function provideInvalidFormData(): iterable
    {
        yield 'blank name' => [self::createFormData(name: '')];
        yield 'blank description' => [self::createFormData(description: '')];
        yield 'no cover' => [self::createFormData()];
        yield 'wrong provider' => [self::createFormData(
            coverFile: self::fakeImage(),
            medias: [
                [
                    'type' => 'video',
                    'provider' => VideoProvider::Dailymotion->value,
                    'url' => 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
                ],
            ]
        )];
        yield 'wrong url' => [self::createFormData(
            coverFile: self::fakeImage(),
            medias: [
                [
                    'type' => 'video',
                    'provider' => VideoProvider::Youtube->value,
                    'url' => 'https://www.fail.com',
                ],
            ]
        )];
        yield 'no image' => [self::createFormData(
            coverFile: self::fakeImage(),
            medias: [
                [
                    'type' => 'image',
                    'alt' => 'image',
                ],
            ]
        )];
        yield 'blank alt' => [self::createFormData(
            coverFile: self::fakeImage(),
            medias: [
                [
                    'type' => 'image',
                    'alt' => '',
                    'file' => self::fakeImage(),
                ],
            ]
        )];
    }

    private static function createFormData(
        string $name = 'Trick',
        string $description = 'Description',
        ?UploadedFile $coverFile = null,
        array $medias = []
    ): array {
        return [
            'parameters' => [
                'trick' => [
                    'name' => $name,
                    'description' => $description,
                    'category' => 1,
                    'medias' => array_map(
                        static fn (array $media): array => array_filter(
                            $media,
                            static fn (string $key): bool => 'file' !== $key,
                            ARRAY_FILTER_USE_KEY
                        ),
                        $medias
                    ),
                ],
            ],
            'files' => [
                'trick' => [
                    'coverFile' => $coverFile,
                    'medias' => array_map(
                        static fn (array $media): array => isset($media['file']) ? ['file' => $media['file']] : [],
                        array_filter($medias, static fn (array $media): bool => 'image' === $media['type'])
                    ),
                ],
            ],
        ];
    }
}
