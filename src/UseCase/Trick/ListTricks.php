<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ListTricks implements ListTricksInterface
{
    public function __construct(private TrickRepository $trickRepository, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function __invoke(int $page): array
    {
        $total = $this->trickRepository->count([]);

        $tricks = $this->trickRepository->getTricksByPage($page);

        $pages = (int) ceil($total / 10);

        $links = [
            'self' => [
                'href' => $this->urlGenerator->generate('api_trick_get_collection', ['page' => $page]),
            ],
        ];

        if ($page < $pages) {
            $links['next'] = [
                'href' => $this->urlGenerator->generate('api_trick_get_collection', ['page' => $page + 1]),
            ];
        }

        return [
            'page' => $page,
            'limit' => 10,
            'pages' => $pages,
            'total' => $total,
            'count' => count($tricks),
            '_links' => $links,
            '_embedded' => ['tricks' => $tricks],
        ];
    }
}
