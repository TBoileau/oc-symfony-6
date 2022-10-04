<?php

declare(strict_types=1);

namespace App\Controller;

use App\Doctrine\Entity\User;
use App\Form\UpdatePasswordType;
use App\UseCase\Profile\UpdatePasswordInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile', name: 'profile_')]
#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    #[Route('/update-password', name: 'update_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function updatePassword(Request $request, UpdatePasswordInterface $editPassword): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UpdatePasswordType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editPassword($user);

            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');

            return $this->redirectToRoute('profile_update_password');
        }

        return $this->renderForm('profile/update_password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
