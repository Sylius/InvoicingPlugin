<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/tests/TestApplication/bundles.php';
        
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 3);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../TestApplication/config/config.yaml');
        
        if (is_file(\dirname(__DIR__) . '/TestApplication/config/services_test.php')) {
            $container->import('../TestApplication/config/services_test.php');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../TestApplication/config/routes.yaml');
    }
}