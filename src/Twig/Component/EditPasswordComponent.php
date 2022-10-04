<?php

declare(strict_types=1);

namespace App\Twig\Component;

use App\Doctrine\Entity\User;
use App\Form\EditPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('edit_password')]
final class EditPasswordComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public User $user;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(EditPasswordType::class, $this->user);
    }
}
