<?php

declare(strict_types=1);

namespace App\Tests\Trick;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends WebTestCase
{
    public function testShouldShowTrick(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/trick-1');
        self::assertResponseIsSuccessful();
    }

    public function testShouldRaiseHttpNotFoundException(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/fail');
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
