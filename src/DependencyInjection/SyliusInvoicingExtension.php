<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusInvoicingExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('sylius_invoicing.pdf_generator.allowed_files', $config['pdf_generator']['allowed_files']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->getCurrentConfiguration($container);

        $container->setParameter('sylius_invoicing.pdf_generator.enabled', $config['pdf_generator']['enabled']);

        $this->registerResources('sylius_invoicing_plugin', 'doctrine/orm', $config['resources'], $container);

        $this->prependDoctrineMigrations($container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\InvoicingPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusInvoicingPlugin/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return ['Sylius\Bundle\CoreBundle\Migrations'];
    }

    private function getCurrentConfiguration(ContainerBuilder $container): array
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $configs = $container->getExtensionConfig($this->getAlias());

        return $this->processConfiguration($configuration, $configs);
    }
}
