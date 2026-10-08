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
use Sylius\InvoicingPlugin\Generator\InvoicingAllowedFilesOptionsProcessor;
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class SyliusInvoicingExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');

        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('sylius_invoicing.pdf_generator.allowed_files', $config['pdf_generator']['allowed_files']);

        // TODO: Remove in 3.0 — once the legacy PDF generator is dropped, the service should be defined directly with its non-legacy arguments instead of being rewired here.
        if (!$config['pdf_generator']['legacy']) {
            $container->getDefinition('sylius_invoicing.generator.invoice_pdf_file')
                ->replaceArgument(0, new Reference(TwigToPdfRendererInterface::class))
            ;

            $container->getDefinition('sylius_invoicing.creator.invoice')
                ->replaceArgument(4, new Reference(PdfFileManagerInterface::class))
            ;

            $container->getDefinition('sylius_invoicing.provider.invoice_file')
                ->replaceArgument(1, new Reference(PdfFileManagerInterface::class))
                ->replaceArgument(3, null)
                ->replaceArgument(4, null)
            ;

            $this->registerAllowedFilesProcessor($container, $config['pdf_generator']['allowed_files']);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->getCurrentConfiguration($container);

        $container->setParameter('sylius_invoicing.pdf_generator.enabled', $config['pdf_generator']['enabled']);

        // TODO: Remove in 3.0 — once the legacy PDF generator is dropped, the bundle configuration should be prepended unconditionally.
        if (!$config['pdf_generator']['legacy']) {
            $this->prependPdfBundleConfiguration($container);
        }

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

    private function prependPdfBundleConfiguration(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('sylius_pdf_generation', [
            'contexts' => [
                'sylius_invoicing' => [
                    'adapter' => 'knp_snappy',
                    'storage' => [
                        'type' => 'gaufrette',
                        'filesystem' => 'gaufrette.sylius_invoicing_invoice_filesystem',
                        'local_cache_directory' => '%kernel.cache_dir%/sylius_invoicing_pdf/',
                    ],
                ],
            ],
        ]);
    }

    /** @param list<string> $allowedFiles */
    private function registerAllowedFilesProcessor(ContainerBuilder $container, array $allowedFiles): void
    {
        if ([] === $allowedFiles) {
            return;
        }

        $definition = new Definition(InvoicingAllowedFilesOptionsProcessor::class, [
            new Reference('file_locator'),
            $allowedFiles,
        ]);
        $definition->addTag('sylius_pdf_generation.options_processor', [
            'adapter' => 'knp_snappy',
            'context' => 'sylius_invoicing',
        ]);

        $container->setDefinition('sylius_invoicing.options_processor.knp_snappy.allowed_files', $definition);
    }
}
