<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Bundle\CoreBundle\Application\Kernel as SyliusKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Webmozart\Assert\Assert;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles(): iterable
    {
        foreach ($this->getBundleListFiles() as $file) {
            yield from $this->registerBundlesFromFile($file);
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        foreach ($this->getBundleListFiles() as $file) {
            $container->addResource(new FileResource($file));
        }

        $container->setParameter('container.dumper.inline_class_loader', true);

        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadContainerConfiguration($loader, $confDir);
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadRoutesConfiguration($routes, $confDir);
        }
    }

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment() && class_exists(MockerContainer::class)) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    protected function getContainerLoader(ContainerInterface $container): LoaderInterface
    {
        /** @var ContainerBuilder $container */
        Assert::isInstanceOf($container, ContainerBuilder::class);
        $locator = new FileLocator($this);
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new GlobFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
            new ClosureLoader($container),
        ));
        return new DelegatingLoader($resolver);
    }

    private function isTestEnvironment(): bool
    {
        return 0 === strpos($this->getEnvironment(), 'test');
    }


    private function loadContainerConfiguration(LoaderInterface $loader, string $confDir): void
    {
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    private function loadRoutesConfiguration(RouteCollectionBuilder $routes, string $confDir): void
    {
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    /**
     * @return BundleInterface[]
     */
    private function registerBundlesFromFile(string $bundlesFile): iterable
    {
        $contents = require $bundlesFile;

        if (SyliusKernel::MINOR_VERSION > 11) {
            $contents = array_merge(
                ['League\FlysystemBundle\FlysystemBundle' => ['all' => true]],
                $contents,
            );
        } else {
            $contents = array_merge(
                ['Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle' => ['all' => true]],
                $contents,
            );
        }

        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    private function getBundleListFiles(): array
    {
        return array_filter(
            array_map(
                static function (string $directory): string {
                    return $directory . '/bundles.php';
                },
                $this->getConfigurationDirectories()
            ),
            'file_exists'
        );
    }

    /**
     * @return string[]
     */
    private function getConfigurationDirectories(): array
    {
        $directories = [
            $this->getProjectDir() . '/config',
            $this->getProjectDir() . '/config/sylius/' . SyliusKernel::MAJOR_VERSION . '.' . SyliusKernel::MINOR_VERSION,
        ];

        return array_filter($directories, 'file_exists');
    }
}
