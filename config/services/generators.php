<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\InvoicingPlugin\Creator\InvoiceCreator;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreator;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreatorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\PdfOptionsGenerator;
use Sylius\InvoicingPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\SequentialInvoiceNumberGenerator;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGenerator;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\UuidInvoiceIdentifierGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_invoicing.generator.invoice_number', SequentialInvoiceNumberGenerator::class)
        ->args([
            service('sylius_invoicing.repository.invoice_sequence'),
            service('sylius_invoicing.factory.invoice_sequence'),
            service('sylius_invoicing.manager.invoice_sequence'),
            service('clock'),
        ]);

    $services->set('sylius_invoicing.generator.invoice_identifier', UuidInvoiceIdentifierGenerator::class);

    $services->set('sylius_invoicing.generator.invoice', InvoiceGenerator::class)
        ->args([
            service('sylius_invoicing.generator.invoice_identifier'),
            service('sylius_invoicing.generator.invoice_number'),
            service('sylius_invoicing.custom_factory.invoice'),
            service('sylius_invoicing.factory.billing_data'),
            service('sylius_invoicing.factory.shop_billing_data'),
            service('sylius_invoicing.converter.order_item_units_to_line_items'),
            service('sylius_invoicing.converter.shipping_adjustments_to_line_items'),
            service('sylius_invoicing.converter.tax_items'),
        ]);

    $services->alias(InvoiceGeneratorInterface::class, 'sylius_invoicing.generator.invoice');

    $services->set('sylius_invoicing.generator.invoice_file_name', InvoiceFileNameGenerator::class);

    $services->alias(InvoiceFileNameGeneratorInterface::class, 'sylius_invoicing.generator.invoice_file_name');

    $services->set('sylius_invoicing.generator.invoice_pdf_file', InvoicePdfFileGenerator::class)
        ->args([
            service('sylius_invoicing.generator.twig_to_pdf'),
            service('file_locator'),
            service('sylius_invoicing.generator.invoice_file_name'),
            '@SyliusInvoicingPlugin/shared/download/pdf.html.twig',
            '%sylius_invoicing.template.logo_file%',
        ]);

    $services->alias(InvoicePdfFileGeneratorInterface::class, 'sylius_invoicing.generator.invoice_pdf_file');

    $services->set('sylius_invoicing.creator.invoice', InvoiceCreator::class)
        ->args([
            service('sylius_invoicing.repository.invoice'),
            service('sylius.repository.order'),
            service('sylius_invoicing.generator.invoice'),
            service('sylius_invoicing.generator.invoice_pdf_file'),
            service('sylius_invoicing.manager.invoice_file'),
            '%sylius_invoicing.pdf_generator.enabled%',
        ]);

    $services->alias(InvoiceCreatorInterface::class, 'sylius_invoicing.creator.invoice');

    $services->set('sylius_invoicing.creator.mass_invoices', MassInvoicesCreator::class)
        ->args([
            service('sylius_invoicing.creator.invoice'),
            service('clock'),
        ]);

    $services->alias(MassInvoicesCreatorInterface::class, 'sylius_invoicing.creator.mass_invoices');

    $services->set('sylius_invoicing.generator.pdf_options', PdfOptionsGenerator::class)
        ->args([
            service('file_locator'),
            '%knp_snappy.pdf.options%',
            '%sylius_invoicing.pdf_generator.allowed_files%',
        ])
        ->deprecate('sylius/invoicing-plugin', '2.2', 'The "%service_id%" service is deprecated. PDF options are now handled by sylius/pdf-generation-bundle adapters.');

    $services->alias(PdfOptionsGeneratorInterface::class, 'sylius_invoicing.generator.pdf_options');

    $services->set('sylius_invoicing.generator.twig_to_pdf', TwigToPdfGenerator::class)
        ->args([
            service('twig'),
            service('knp_snappy.pdf'),
            service('sylius_invoicing.generator.pdf_options'),
        ])
        ->deprecate('sylius/invoicing-plugin', '2.2', 'The "%service_id%" service is deprecated. Use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface from sylius/pdf-generation-bundle instead.');

    $services->alias(TwigToPdfGeneratorInterface::class, 'sylius_invoicing.generator.twig_to_pdf');
};
