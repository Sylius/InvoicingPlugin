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
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\InvoicingPlugin\DependencyInjection\SyliusInvoicingExtension;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceSequence;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\TaxItem;
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
    public function it_prepends_configuration_with_invoice_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.invoice.class',
            Invoice::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.invoice',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_billing_data_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.billing_data.class',
            BillingData::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.billing_data',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_shop_billing_data_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.shop_billing_data.class',
            InvoiceShopBillingData::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.shop_billing_data',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_line_item_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.line_item.class',
            LineItem::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.line_item',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_tax_item_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.tax_item.class',
            TaxItem::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.tax_item',
            ResourceController::class
        );
    }

    /** @test */
    public function it_prepends_configuration_with_invoice_sequence_resource_services(): void
    {
        $this->prepend();

        $this->assertContainerBuilderHasParameter(
            'sylius_invoicing_plugin.model.invoice_sequence.class',
            InvoiceSequence::class
        );

        $this->assertContainerBuilderHasService(
            'sylius_invoicing_plugin.controller.invoice_sequence',
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
