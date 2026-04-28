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

namespace Tests\Sylius\InvoicingPlugin\DependencyInjection;

use Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\InvoicingPlugin\DependencyInjection\SyliusInvoicingExtension;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceSequence;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Generator\InvoicingAllowedFilesOptionsProcessor;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGenerator;
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use SyliusLabs\DoctrineMigrationsExtraBundle\DependencyInjection\SyliusLabsDoctrineMigrationsExtraExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class SyliusInvoicingExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_prepending_doctrine_migration_with_proper_migrations_paths(): void
    {
        $this->configureContainer();

        $this->prepend();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertTrue(isset(
            $doctrineMigrationsExtensionConfig[0]['migrations_paths']['Sylius\InvoicingPlugin\Migrations']
        ));
        self::assertSame(
            '@SyliusInvoicingPlugin/src/Migrations',
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

    /** @test */
    public function it_does_not_autoconfigure_prepending_doctrine_migrations_if_it_is_disabled(): void
    {
        $this->configureContainer();

        $this->container->setParameter('sylius_core.prepend_doctrine_migrations', false);

        $this->prepend();

        $doctrineMigrationsExtensionConfig = $this->container->getExtensionConfig('doctrine_migrations');

        self::assertEmpty($doctrineMigrationsExtensionConfig);

        $syliusLabsDoctrineMigrationsExtraExtensionConfig = $this
            ->container
            ->getExtensionConfig('sylius_labs_doctrine_migrations_extra')
        ;

        self::assertEmpty($syliusLabsDoctrineMigrationsExtraExtensionConfig);
    }

    /** @test */
    public function it_loads_allowed_files_for_pdf_generator_configuration(): void
    {
        $this->load(['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']]]);

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.pdf_generator.allowed_files',
            ['swans.png', 'product.png']
        );
    }

    /** @test */
    public function it_prepends_configuration_with_enabled_pdf_generator(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_invoicing',
            ['pdf_generator' => ['enabled' => false]]
        );

        $this->prepend();

        $this->assertContainerBuilderHasParameter('sylius_invoicing.pdf_generator.enabled', false);
    }

    /** @test */
    public function it_does_not_prepend_sylius_pdf_configuration_when_legacy_is_enabled(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_invoicing',
            ['pdf_generator' => ['allowed_files' => ['swans.png'], 'legacy' => true]],
        );

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertEmpty($syliusPdfConfig);
    }

    /** @test */
    public function it_prepends_sylius_pdf_context_configuration_when_legacy_is_disabled(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_invoicing',
            ['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png'], 'legacy' => false]],
        );

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertNotEmpty($syliusPdfConfig);
        self::assertSame(
            [
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
            ],
            $syliusPdfConfig[0],
        );
    }

    /** @test */
    public function it_prepends_sylius_pdf_context_without_options_when_no_allowed_files(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_invoicing',
            ['pdf_generator' => ['legacy' => false]],
        );

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertNotEmpty($syliusPdfConfig);
        self::assertSame(
            [
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
            ],
            $syliusPdfConfig[0],
        );
    }

    /** @test */
    public function it_keeps_legacy_twig_to_pdf_generator_when_legacy_is_enabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => true]]);

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.generator.twig_to_pdf',
            TwigToPdfGenerator::class,
        );
    }

    /** @test */
    public function it_replaces_invoice_pdf_file_generator_argument_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_invoicing.generator.invoice_pdf_file');

        self::assertEquals(
            TwigToPdfRendererInterface::class,
            (string) $definition->getArgument(0),
        );
    }

    /** @test */
    public function it_replaces_invoice_creator_file_manager_argument_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_invoicing.creator.invoice');

        self::assertEquals(
            PdfFileManagerInterface::class,
            (string) $definition->getArgument(4),
        );
    }

    /** @test */
    public function it_replaces_invoice_file_provider_arguments_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        $definition = $this->container->getDefinition('sylius_invoicing.provider.invoice_file');

        self::assertEquals(
            PdfFileManagerInterface::class,
            (string) $definition->getArgument(1),
        );
        self::assertNull($definition->getArgument(3));
        self::assertNull($definition->getArgument(4));
    }

    /** @test */
    public function it_does_not_replace_creator_or_provider_arguments_when_legacy_is_enabled(): void
    {
        $this->load(['pdf_generator' => ['legacy' => true]]);

        $creatorDefinition = $this->container->getDefinition('sylius_invoicing.creator.invoice');
        self::assertEquals(
            'sylius_invoicing.manager.invoice_file',
            (string) $creatorDefinition->getArgument(4),
        );

        $providerDefinition = $this->container->getDefinition('sylius_invoicing.provider.invoice_file');
        self::assertEquals(
            'gaufrette.sylius_invoicing_invoice_filesystem',
            (string) $providerDefinition->getArgument(1),
        );
        self::assertEquals(
            'sylius_invoicing.manager.invoice_file',
            (string) $providerDefinition->getArgument(3),
        );
    }

    /** @test */
    public function it_prepends_sylius_pdf_storage_configuration_when_legacy_is_disabled(): void
    {
        $this->container->prependExtensionConfig(
            'sylius_invoicing',
            ['pdf_generator' => ['legacy' => false]],
        );

        $this->container->setParameter('sylius_invoicing.invoice_save_path', '/tmp/invoices/');

        $this->prepend();

        $syliusPdfConfig = $this->container->getExtensionConfig('sylius_pdf_generation');

        self::assertNotEmpty($syliusPdfConfig);
        self::assertSame(
            [
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
            ],
            $syliusPdfConfig[0],
        );
    }

    /** @test */
    public function it_registers_allowed_files_options_processor_when_legacy_is_disabled(): void
    {
        $this->load(['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png'], 'legacy' => false]]);

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.options_processor.knp_snappy.allowed_files',
            InvoicingAllowedFilesOptionsProcessor::class,
        );

        $definition = $this->container->getDefinition('sylius_invoicing.options_processor.knp_snappy.allowed_files');
        $tags = $definition->getTag('sylius_pdf_generation.options_processor');

        self::assertCount(1, $tags);
        self::assertSame('knp_snappy', $tags[0]['adapter']);
        self::assertSame('sylius_invoicing', $tags[0]['context']);
    }

    /** @test */
    public function it_does_not_register_allowed_files_options_processor_when_no_allowed_files(): void
    {
        $this->load(['pdf_generator' => ['legacy' => false]]);

        self::assertFalse($this->container->hasDefinition('sylius_invoicing.options_processor.knp_snappy.allowed_files'));
    }

    /** @test */
    public function it_does_not_register_allowed_files_options_processor_when_legacy_is_enabled(): void
    {
        $this->load(['pdf_generator' => ['allowed_files' => ['swans.png'], 'legacy' => true]]);

        self::assertFalse($this->container->hasDefinition('sylius_invoicing.options_processor.knp_snappy.allowed_files'));
    }

    /** @test */
    public function it_prepends_configuration_with_invoice_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.invoice.class',
            Invoice::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.invoice',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_billing_data_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.billing_data.class',
            BillingData::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.billing_data',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_shop_billing_data_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.shop_billing_data.class',
            InvoiceShopBillingData::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.shop_billing_data',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_line_item_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.line_item.class',
            LineItem::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.line_item',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_tax_item_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.tax_item.class',
            TaxItem::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.tax_item',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_invoice_sequence_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing.model.invoice_sequence.class',
            InvoiceSequence::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing.controller.invoice_sequence',
            ResourceController::class
        );
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
    }

    private function prepend(): void
    {
        foreach ($this->container->getExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($this->container);
            }
        }
    }
}
