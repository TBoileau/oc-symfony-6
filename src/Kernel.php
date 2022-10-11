<?php

declare(strict_types=1);

namespace App;

use App\DependencyInjection\CompilerPass\DoctrinePass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

use function date_default_timezone_set;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        parent::boot();

        date_default_timezone_set('Europe/Paris');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DoctrinePass());
    }
}
