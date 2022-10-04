<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Tests\WebTestCaseHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LoginTest extends WebTestCase
{
    use WebTestCaseHelperTrait;

    public function testShouldAuthenticatedUserAndRedirectToIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/login');
        $client->submitForm('Se connecter', self::createFormData());

        self::assertResponseRedirects('http://localhost/');
        self::assertIsAuthenticated(true);
    }

    public function testShouldNotAuthenticatedUserDueToRegistrationNotValidatedAndRedirectToIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/login');
        $client->submitForm('Se connecter', self::createFormData(email: 'user+6@email.com'));

        self::assertResponseRedirects('http://localhost/login');
        self::assertIsAuthenticated(false);
    }

    public function testShouldNotAuthenticatedAnUserThatNotValidateItsRegistrationAndRedirectToIndex(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/login');
        $client->submitForm('Se connecter', self::createFormData(email: 'user+11@email.com'));

        self::assertIsAuthenticated(false);
        self::assertResponseRedirects('http://localhost/login');
    }

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array{email: string, password: string} $formData
     */
    public function testShouldNotAuthenticatedUserAndRedirectToLogin(array $formData): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/login');
        $client->submitForm('Se connecter', $formData);

        self::assertIsAuthenticated(false);
        self::assertResponseRedirects('http://localhost/login');
    }

    /**
     * @return array{_username: string, _password: string}
     */
    private static function createFormData(string $email = 'user+1@email.com', string $password = 'password'): array
    {
        return ['_username' => $email, '_password' => $password];
    }

    /**
     * @return iterable<string, array<array-key, array{_username: string, _password: string}>>
     */
    public function provideInvalidFormData(): iterable
    {
        yield 'invalid email' => [self::createFormData(email: 'fail@email.com')];
        yield 'invalid password' => [self::createFormData(password: 'fail')];
    }
}
