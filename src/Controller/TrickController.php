<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\Comment;
use App\Doctrine\Entity\Trick;
use App\Doctrine\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Security\Voter\TrickVoter;
use App\UseCase\Trick\CommentTrickInterface;
use App\UseCase\Trick\CreateTrickInterface;
use App\UseCase\Trick\DeleteTrickInterface;
use App\UseCase\Trick\UpdateTrickInterface;
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

    #[Route('/create', name: 'create', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, CreateTrickInterface $createTrick): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $trick = new Trick();
        $trick->setUser($user);

        $form = $this->createForm(
            TrickType::class,
            $trick,
            ['validation_groups' => ['cover', 'image', 'Default']]
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $createTrick($trick);

            $this->addFlash('success', 'La figure a été ajoutée avec succès.');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('trick/create.html.twig', [
            'form' => $form,
            'trick' => $trick,
        ]);
    }

    #[Route('/{slug}/update', name: 'update', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted('ROLE_USER')]
    public function update(Trick $trick, Request $request, UpdateTrickInterface $updateTrick): Response
    {
        $form = $this->createForm(TrickType::class, $trick)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updateTrick($trick);

            $this->addFlash('success', 'La figure a été modifiée avec succès.');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->renderForm('trick/update.html.twig', [
            'form' => $form,
            'trick' => $trick,
        ]);
    }

    #[Route('/{slug}/delete', name: 'delete', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    #[IsGranted(TrickVoter::DELETE, subject: 'trick')]
    public function delete(Trick $trick, Request $request, DeleteTrickInterface $delete): Response
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
