<?php

declare(strict_types=1);

namespace App\UseCase\Trick;

use App\Entity\Trick;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ListComments implements ListCommentsInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __invoke(Trick $trick, int $page): array
    {
        $total = $this->commentRepository->count(['trick' => $trick]);

        $comments = $this->commentRepository->getCommentsByTrickAndPage($trick, $page);

        $limit = 5;

        $pages = (int) ceil($total / $limit);

        $links = [
            'self' => [
                'href' => $this->urlGenerator->generate(
                    'api_trick_comments',
                    ['page' => $page, 'id' => $trick->getId()]
                ),
            ],
        ];

        if ($page < $pages) {
            $links['next'] = [
                'href' => $this->urlGenerator->generate(
                    'api_trick_comments',
                    ['page' => $page + 1, 'id' => $trick->getId()]
                ),
            ];
        }

        return [
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages,
            'total' => $total,
            'count' => count($comments),
            '_links' => $links,
            '_embedded' => ['comments' => $comments],
        ];
    }
}
