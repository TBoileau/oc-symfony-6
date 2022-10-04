<?php

declare(strict_types=1);

namespace App\Mailer;

use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmailSender implements EmailSenderInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $options = [];

    public function __construct(
        private MailerInterface $mailer,
        private string $sender,
        private ContainerInterface $container
    ) {
    }

    public function with(string $name, mixed $value): EmailSenderInterface
    {
        if (isset($this->options[$name])) {
            throw new \InvalidArgumentException(sprintf('Option "%s" is already set.', $name)); // @codeCoverageIgnore
        }

        $this->options[$name] = $value;

        return $this;
    }

    public function send(string $emailClass): void
    {
        if (!$this->container->has($emailClass)) {
            throw new \InvalidArgumentException(sprintf('Email "%s" must implement EmailInterface.', $emailClass)); // @codeCoverageIgnore
        }

        /** @var EmailInterface $email */
        $email = $this->container->get($emailClass);

        $resolver = new OptionsResolver();
        $email->configureOptions($resolver);
        $options = $resolver->resolve($this->options);

        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->sender, 'Snowtricks'));

        $email->build($templatedEmail, $options);

        $this->mailer->send($templatedEmail);
    }
}
