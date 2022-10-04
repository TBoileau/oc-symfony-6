<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\User;
use App\Entity\ResetPasswordrequest;
use App\Tests\WebTestCaseHelperTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\RawMessage;

final class RequestResetPasswordTest extends WebTestCase
{
    use WebTestCaseHelperTrait;
    use MailerAssertionsTrait;

    public function testShouldCreateResetPasswordRequestAndRedirectToIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reset-password/request');
        $client->submitForm('Réinitialiser', self::createFormData());

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'user+1@email.com']);

        /** @var ?ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $entityManager->getRepository(ResetPasswordRequest::class)->findOneBy(['user' => $user]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/');
        self::assertNotNull($resetPasswordRequest);
        self::assertEmailCount(1);

        /** @var RawMessage $email */
        $email = self::getMailerMessage();

        self::assertEmailHtmlBodyContains($email, (string) $resetPasswordRequest->getToken());
        self::assertEmailAddressContains($email, 'To', 'user+1@email.com');
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{
     *      'reset_password_request[email]': string
     * } $formData
     */
    public function testShouldRaiseFormErrors(array $formData): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/reset-password/request');
        $client->submitForm('Réinitialiser', $formData);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return iterable<string, array<array-key, array{
     *      'reset_password_request[user]': string
     * }>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'blank email' => [self::createFormData(email: '')];
        yield 'invalid email' => [self::createFormData(email: 'fail')];
        yield 'non existing email' => [self::createFormData(email: 'fail@email.com')];
    }

    /**
     * @return array{
     *      'reset_password_request[user]': string
     * }
     */
    private static function createFormData(string $email = 'user+1@email.com'): array
    {
        return ['reset_password_request[user]' => $email];
    }
}
