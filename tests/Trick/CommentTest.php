<?php

declare(strict_types=1);

namespace App\Tests\Trick;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CommentTest extends WebTestCase
{
    public function testShouldAddCommentToTrickAndReturnToTrickPage(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        /** @var Trick $trick */
        $trick = $entityManager->getRepository(Trick::class)->find(1);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/trick-1');
        $client->submitForm('Envoyer', self::createFormData());

        /** @var array<array-key, Comment> $comments */
        $comments = $entityManager->getRepository(Comment::class)->findBy(['trick' => $trick], ['createdAt' => 'DESC']);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/trick-1');
        self::assertCount(11, $comments);
        self::assertSame($user->getId(), $comments[0]->getUser()->getId());
        self::assertSame('Content', $comments[0]->getContent());
    }

    public function testShouldRaiseFormErrors(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, '/trick-1');
        $client->submitForm('Envoyer', self::createFormData(content: ''));
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return array{
     *      'comment[content]': string
     * }
     */
    private static function createFormData(string $content = 'Content'): array
    {
        return [
            'comment[content]' => $content,
        ];
    }
}
