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
use Sylius\InvoicingPlugin\Generator\TwigToGotenbergPdfGenerator;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class SyliusInvoicingExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.xml');

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('sylius_invoicing.pdf_generator.allowed_files', $config['pdf_generator']['allowed_files']);
        $container->setParameter('sylius_invoicing.pdf_generator.generate_with_gotenberg', $config['pdf_generator']['generate_with_gotenberg']);

        if ($config['pdf_generator']['generate_with_gotenberg'] === true) {
            $definition = new Definition(TwigToGotenbergPdfGenerator::class);
            $definition->addArgument(new Reference('sensiolabs_gotenberg'));
            $definition->setDecoratedService('sylius_invoicing.generator.twig_to_pdf');

            $container->setDefinition('sylius_invoicing.gotenberg_generator.twig_to_pdf', $definition);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->getCurrentConfiguration($container);

        $container->setParameter('sylius_invoicing.pdf_generator.enabled', $config['pdf_generator']['enabled']);

        $this->registerResources('sylius_invoicing', 'doctrine/orm', $config['resources'], $container);

        $this->prependDoctrineMigrations($container);
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Sylius\InvoicingPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@SyliusInvoicingPlugin/src/Migrations';
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
