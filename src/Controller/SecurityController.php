<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\UseCase\Security\RegisterInterface;
use App\UseCase\Security\RequestResetPasswordInterface;
use App\UseCase\Security\ResetPasswordInterface;
use App\UseCase\Security\ValidRegistrationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

    #[Route('/register', name: 'register', methods: [Request::METHOD_GET, Request::METHOD_POST])]
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

    #[Route('/register/{registrationToken}/valid', name: 'valid_registration', methods: [Request::METHOD_GET])]
    public function validRegistration(User $user, ValidRegistrationInterface $validRegistration): RedirectResponse
    {
        $validRegistration($user);

        $this->addFlash('success', 'Votre inscription a été validée avec succès.');

        return $this->redirectToRoute('security_login');
    }

    #[Route('/reset-password/request', name: 'reset_password_request', methods: [Request::METHOD_GET, Request::METHOD_POST])]
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

    #[Route('/reset-password/{token}', name: 'reset_password', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function resetPassword(
        ResetPasswordRequest $resetPasswordRequest,
        Request $request,
        ResetPasswordInterface $resetPassword
    ): Response {
        if ($resetPasswordRequest->isExpired()) {
            throw new BadRequestHttpException('Le lien de réinitialisation de mot de passe a expiré.');
        }

        $form = $this->createForm(ResetPasswordType::class, $resetPasswordRequest->getUser())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resetPassword($resetPasswordRequest);

            $this->addFlash('success', 'Votre mot de passe a été moidifié avec succès.');

            return $this->redirectToRoute('security_login');
        }

        return $this->renderForm('security/reset_password.html.twig', [
            'form' => $form,
            'user' => $resetPasswordRequest->getUser(),
        ]);
    }
}
