<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\UseCase\Trick\CommentTrickInterface;
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

    #[Route('/{slug}', name: 'show', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function show(Trick $trick, Request $request, CommentTrickInterface $commentTrick): Response
    {
        $comment = new Comment();
        $comment->setTrick($trick);

        $form = $this->createForm(CommentType::class, $comment)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            /** @var User $user */
            $user = $this->getUser();

            $comment->setUser($user);

            $commentTrick($comment);

            $this->addFlash('success', 'Commentaire ajouté avec succès.');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'comment' => $comment,
        ]);
    }
}
