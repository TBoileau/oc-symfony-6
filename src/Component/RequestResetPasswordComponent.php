<?php

declare(strict_types=1);

namespace App\Component;

use App\Entity\ResetPasswordRequest;
use App\Form\ResetPasswordRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('request_reset_password')]
final class RequestResetPasswordComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?ResetPasswordRequest $resetPasswordRequest = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ResetPasswordRequestType::class, $this->resetPasswordRequest);
    }
}
