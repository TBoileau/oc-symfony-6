<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Security\Voter\TrickVoter;
use App\UseCase\Trick\CommentTrickInterface;
use App\UseCase\Trick\DeleteInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    #[Route('/{slug}/delete', name: 'delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(TrickVoter::DELETE, subject: 'trick')]
    public function delete(Trick $trick, Request $request, DeleteInterface $delete): Response
    {
        $form = $this->createFormBuilder()->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $delete($trick);

            $this->addFlash('success', 'La figure a été supprimée avec succès.');

            return $this->redirectToRoute('trick_list');
        }

        return $this->renderForm('trick/delete.html.twig', ['form' => $form, 'trick' => $trick]);
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
