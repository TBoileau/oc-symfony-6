<?php

declare(strict_types=1);

namespace App\Twig\Component\Form;

use App\Doctrine\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('registration', template: 'components/form/registration.html.twig')]
final class RegistrationComponent extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(fieldName: 'data')]
    public ?User $user = null;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RegistrationType::class, $this->user);
    }
}
