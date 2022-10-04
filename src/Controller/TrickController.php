<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'trick_')]
final class TrickController extends AbstractController
{
    #[Route(name: 'list', methods: [Request::METHOD_GET])]
    public function list(): Response
    {
        return $this->render('trick/list.html.twig');
    }

    #[Route('/{slug}', name: 'show', methods: [Request::METHOD_GET])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', ['trick' => $trick]);
    }
}
