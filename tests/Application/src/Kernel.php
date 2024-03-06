<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
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
use Webmozart\Assert\Assert;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

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

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment() && class_exists(MockerContainer::class)) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
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
        return str_starts_with($this->getEnvironment(), 'test');
    }

    /**
     * @return BundleInterface[]
     */
    private function registerBundlesFromFile(string $bundlesFile): iterable
    {
        $contents = require $bundlesFile;

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
            $this->getProjectDir() . '/config/sylius/' . SyliusCoreBundle::MAJOR_VERSION . '.' . SyliusCoreBundle::MINOR_VERSION,
        ];

        return array_filter($directories, 'file_exists');
    }
}
