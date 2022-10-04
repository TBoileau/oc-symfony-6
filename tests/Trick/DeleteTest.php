<?php

declare(strict_types=1);

namespace App\Tests\Trick;

use App\Entity\Trick;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTest extends WebTestCase
{
    public function testShouldDeleteTrick(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);


        $client->request(Request::METHOD_GET, '/trick-1/delete');
        self::assertResponseIsSuccessful();
        $client->submitForm('Supprimer');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/');

        /** @var ?Trick $trick */
        $trick = $entityManager->getRepository(Trick::class)->find(1);

        self::assertNull($trick);
    }

    public function testShouldRaiseHttpForbiddenException(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+2@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/trick-1/delete');
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testShouldRaiseHttpAccessDeniedExceptionAndRedirectToLogin(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/trick-1/delete');
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('http://localhost/login');
    }
}
