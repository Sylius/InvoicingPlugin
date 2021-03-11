<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use FOS\OAuthServerBundle\DependencyInjection\FOSOAuthServerExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\InvoicingPlugin\DependencyInjection\SyliusInvoicingExtension;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class SyliusInvoicingExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_autoconfigures_prepending_doctrine_migration_with_proper_migrations_paths(): void
    {
        $this->configureContainer();

        $this->customLoad();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertTrue(isset(
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\InvoicingPlugin\Migrations']
        ));
        self::assertSame(
            '@SyliusInvoicingPlugin/Migrations',
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\InvoicingPlugin\Migrations']
        );

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        self::assertTrue(isset(
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\InvoicingPlugin\Migrations']
        ));
        self::assertSame(
            'Sylius\Bundle\CoreBundle\Migrations',
            $syliusLabsDoctrineMigrationsExtraExtensionConfig[0]['migrations']['Sylius\InvoicingPlugin\Migrations'][0]
        );
    }

    /**
     * @test
     */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled(): void
    {
        $this->configureContainer();

        $this->container->setParameter('sylius_core.prepend_doctrine_migrations', false);

        $this->customLoad();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertEmpty($doctrineMigrationsExtensionConfig);

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        self::assertEmpty($syliusLabsDoctrineMigrationsExtraExtensionConfig);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusInvoicingExtension()];
    }

    private function configureContainer(): void
    {
        $this->container->setParameter('kernel.environment', 'test');
        $this->container->setParameter('kernel.debug', true);

        $this->container->registerExtension(new DoctrineMigrationsExtension());
        $this->container->registerExtension(new SyliusLabsDoctrineMigrationsExtraExtension());
        $this->container->registerExtension(new FOSOAuthServerExtension());
    }

    private function customLoad(): void
    {
        $configurationValues = ['sylius_invoicing' => []];

        $configs = [$this->getMinimalConfiguration(), $configurationValues];

        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }
        }

        foreach ($this->container->getExtensions() as $extension) {
            $extensionAlias = $extension->getAlias();
            if (isset($configs[$extensionAlias])) {
                $extension->load($configs[$extensionAlias], $this->container);
            }
        }
    }
}
