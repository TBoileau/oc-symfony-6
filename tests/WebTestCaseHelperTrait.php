<?php

declare(strict_types=1);

namespace App\Tests;

use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Symfony\Component\Uid\Uuid;

trait WebTestCaseHelperTrait
{
    private static function getClient(): KernelBrowser
    {
        $method = new ReflectionMethod(WebTestCase::class, 'getClient');

        /** @var KernelBrowser $kernel */
        $kernel = $method->invoke(null);

        return $kernel;
    }

    public function fakeImage(): UploadedFile
    {
        $filesystem = new Filesystem();

        $filename = sprintf('%s/../public/uploads/%s.png', __DIR__, Uuid::v4());

        $filesystem->copy(
            __DIR__.'/../public/uploads/image.png',
            $filename
        );

        return new UploadedFile(
            $filename,
            'image_copy.png',
            'image/png',
            null,
            true
        );
    }

    public function fakeFile(): UploadedFile
    {
        $filesystem = new Filesystem();

        $filename = sprintf('%s/../public/uploads/%s.txt', __DIR__, Uuid::v4());

        $filesystem->copy(
            __DIR__.'/../public/uploads/file.txt',
            $filename
        );

        return new UploadedFile(
            $filename,
            'file_copy.txt',
            'text/plain',
            null,
            true
        );
    }

    public function assertFlashBagContains(string $type, string $message): void
    {
        self::assertContains($message, self::getClient()->getRequest()->getSession()->getFlashBag()->get($type));
    }

    public static function assertIsAuthenticated(bool $isAuthenticated): void
    {
        /** @var Profile $profile */
        $profile = self::getClient()->getProfile();
        $collector = $profile->getCollector('security');
        self::assertInstanceOf(SecurityDataCollector::class, $collector);
        self::assertSame($isAuthenticated, $collector->isAuthenticated());
    }
}
