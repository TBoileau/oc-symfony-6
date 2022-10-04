<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordRequestType;
use App\UseCase\Security\RegisterInterface;
use App\UseCase\Security\RequestResetPasswordInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route(name: 'security_')]
final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, RegisterInterface $register): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $register($user);

            $this->addFlash('success', 'Votre compte a bien été créé.');

            return $this->redirectToRoute('index');
        }

        return $this->renderForm('security/register.html.twig', ['form' => $form, 'user' => $user]);
    }

    #[Route('/register/{registrationToken}/valid', name: 'valid_registration')]
    public function validRegistration(): void
    {
    }

    #[Route('/reset-password/request', name: 'reset_password_request')]
    public function requestResetPassword(Request $request, RequestResetPasswordInterface $requestResetPassword): Response
    {
        $resetPasswordRequest = new ResetPasswordRequest();

        $form = $this->createForm(ResetPasswordRequestType::class, $resetPasswordRequest)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestResetPassword($resetPasswordRequest);

            $this->addFlash('success', 'Votre demande de réinitialisation de mot de passe a été enregistrée avec succès.');

            return $this->redirectToRoute('index');
        }

        return $this->renderForm('security/request_reset_password.html.twig', [
            'form' => $form,
            'reset_password_request' => $resetPasswordRequest,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset_password')]
    public function resetPassword(): void
    {
    }
}
