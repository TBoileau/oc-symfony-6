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

use function sprintf;

final class ValidRegistrationTest extends WebTestCase
{
    use WebTestCaseHelperTrait;
    use MailerAssertionsTrait;

    public function testShouldValidRegistrationAndRedirectToLogin(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+11@email.com']);

        $client->request(
            Request::METHOD_GET,
            sprintf('/register/%s/valid', $user->getRegistrationToken())
        );

        $entityManager->refresh($user);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/login');
        self::assertNull($user->getRegistrationToken());
    }

    public function testShouldRaiseHttpNotFoundExceptionDueToInvalidToken(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/register/fail/valid');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
