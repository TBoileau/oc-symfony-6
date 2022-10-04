<?php

declare(strict_types=1);

namespace App\Tests\Trick;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class ListTest extends WebTestCase
{
    public function testShouldShowHomePage(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');
        self::assertResponseIsSuccessful();
    }
}
