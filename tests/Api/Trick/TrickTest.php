<?php

declare(strict_types=1);

namespace App\Tests\Api\Trick;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

use function sprintf;
use function json_decode;

final class TrickTest extends WebTestCase
{
    /**
     * @param array{self: array{href: string}, next?: array{href: string}} $links
     *
     * @dataProvider provideTricks
     */
    public function testGetCollection(int $page, int $limit, int $pages, int $total, int $count, array $links): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, sprintf('/api/tricks?page=%d', $page));
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/hal+json');

        /** @var string $json */
        $json = $client->getResponse()->getContent();

        /**
         * @var array{
         *      page: int,
         *      pages: int,
         *      limit: int,
         *      total: int,
         *      count: int,
         *      _links: array{
         *          self: array{href: string},
         *          next?: array{href: string}
         *      },
         *      _embedded: array{tricks: array<array-key, array<array-key, mixed>>}
         * } $data
         */
        $data = json_decode($json, true);

        self::assertSame($page, $data['page']);
        self::assertSame($limit, $data['limit']);
        self::assertSame($pages, $data['pages']);
        self::assertSame($total, $data['total']);
        self::assertSame($count, $data['count']);
        self::assertSame($links, $data['_links']);
        self::assertCount($count, $data['_embedded']['tricks']);
    }

    /**
     * @return iterable<string, array{
     *      page: int,
     *      pages: int,
     *      limit: int,
     *      total: int,
     *      count: int,
     *      _links: array{
     *          self: array{href: string},
     *          next?: array{href: string}
     *      }
     * }>
     */
    public function provideTricks(): iterable
    {
        yield 'page 1' => [
            'page' => 1,
            'limit' => 10,
            'pages' => 25,
            'total' => 250,
            'count' => 10,
            '_links' => [
                'self' => [
                    'href' => '/api/tricks?page=1',
                ],
                'next' => [
                    'href' => '/api/tricks?page=2',
                ],
            ],
        ];

        yield 'page 25' => [
            'page' => 25,
            'limit' => 10,
            'pages' => 25,
            'total' => 250,
            'count' => 10,
            '_links' => [
                'self' => [
                    'href' => '/api/tricks?page=25',
                ],
            ],
        ];
    }

    /**
     * @param array{self: array{href: string}, next?: array{href: string}} $links
     *
     * @dataProvider provideComments
     */
    public function testComments(int $page, int $limit, int $pages, int $total, int $count, array $links): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, sprintf('/api/tricks/1/comments?page=%d', $page));
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/hal+json');

        /** @var string $json */
        $json = $client->getResponse()->getContent();

        /**
         * @var array{
         *      page: int,
         *      pages: int,
         *      limit: int,
         *      total: int,
         *      count: int,
         *      _links: array{
         *          self: array{href: string},
         *          next?: array{href: string}
         *      },
         *      _embedded: array{comments: array<array-key, array<array-key, mixed>>}
         * } $data
         */
        $data = json_decode($json, true);

        self::assertSame($page, $data['page']);
        self::assertSame($limit, $data['limit']);
        self::assertSame($pages, $data['pages']);
        self::assertSame($total, $data['total']);
        self::assertSame($count, $data['count']);
        self::assertSame($links, $data['_links']);
        self::assertCount($count, $data['_embedded']['comments']);
    }

    /**
     * @return iterable<string, array{
     *      page: int,
     *      pages: int,
     *      limit: int,
     *      total: int,
     *      count: int,
     *      _links: array{
     *          self: array{href: string},
     *          next?: array{href: string}
     *      }
     * }>
     */
    public function provideComments(): iterable
    {
        yield 'page 1' => [
            'page' => 1,
            'limit' => 5,
            'pages' => 2,
            'total' => 10,
            'count' => 5,
            '_links' => [
                'self' => [
                    'href' => '/api/tricks/1/comments?page=1',
                ],
                'next' => [
                    'href' => '/api/tricks/1/comments?page=2',
                ],
            ],
        ];

        yield 'page 2' => [
            'page' => 2,
            'limit' => 5,
            'pages' => 2,
            'total' => 10,
            'count' => 5,
            '_links' => [
                'self' => [
                    'href' => '/api/tricks/1/comments?page=2',
                ],
            ],
        ];
    }
}
