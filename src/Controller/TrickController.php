<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'trick_')]
final class TrickController extends AbstractController
{
    #[Route(name: 'list')]
    public function list(): Response
    {
        return $this->render('trick/list.html.twig');
    }
}
