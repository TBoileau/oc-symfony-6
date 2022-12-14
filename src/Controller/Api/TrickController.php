<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Doctrine\Entity\Trick;
use App\UseCase\Trick\ListCommentsInterface;
use App\UseCase\Trick\ListTricksInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tricks', name: 'trick_')]
final class TrickController extends AbstractController
{
    #[Route(name: 'get_collection', methods: [Request::METHOD_GET])]
    public function getCollection(ListTricksInterface $listTricks, Request $request): JsonResponse
    {
        return $this->json(
            $listTricks($request->query->getInt('page', 1)),
            Response::HTTP_OK,
            ['Content-Type' => 'application/hal+json'],
            ['groups' => ['trick:read']]
        );
    }

    #[Route('/{id}/comments', name: 'comments', methods: [Request::METHOD_GET])]
    public function getItem(Trick $trick, ListCommentsInterface $listComments, Request $request): JsonResponse
    {
        return $this->json(
            $listComments($trick, $request->query->getInt('page', 1)),
            Response::HTTP_OK,
            ['Content-Type' => 'application/hal+json'],
            ['groups' => ['comment:read']]
        );
    }
}
