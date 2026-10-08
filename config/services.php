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

use Sylius\InvoicingPlugin\CommandHandler\SendInvoiceEmailHandler;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSender;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactory;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManager;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProvider;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProvider;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProvider;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/**/*.php');

    $parameters->set('sylius_invoicing.default_logo_file', '@SyliusInvoicingPlugin/assets/sylius-logo.png');
    $parameters->set('sylius_invoicing.template.logo_file', '%env(default:sylius_invoicing.default_logo_file:resolve:SYLIUS_INVOICING_LOGO_FILE)%');

    $services->set('sylius_invoicing.email.invoice_email_sender', InvoiceEmailSender::class)
        ->args([
            service('sylius.email_sender'),
            service('sylius_invoicing.provider.invoice_file'),
            '%sylius_invoicing.pdf_generator.enabled%',
        ]);

    $services->alias(InvoiceEmailSenderInterface::class, 'sylius_invoicing.email.invoice_email_sender');

    $services->set('sylius_invoicing.command_handler.send_invoice_email', SendInvoiceEmailHandler::class)
        ->args([
            service('sylius_invoicing.repository.invoice'),
            service('sylius.repository.order'),
            service('sylius_invoicing.email.invoice_email_sender'),
        ])
        ->tag('messenger.message_handler');

    $services->set('sylius_invoicing.security.voter.invoice', InvoiceVoter::class)
        ->args([service('sylius.repository.order')])
        ->tag('security.voter');

    $services->set('sylius_invoicing.provider.tax_rate_percentage', TaxRatePercentageProvider::class);

    $services->alias(TaxRatePercentageProviderInterface::class, 'sylius_invoicing.provider.tax_rate_percentage');

    $services->set('sylius_invoicing.custom_factory.invoice', InvoiceFactory::class)
        ->args([
            '%sylius_invoicing.model.invoice.class%',
            service('sylius_invoicing.factory.shop_billing_data'),
        ]);

    $services->alias(InvoiceFactoryInterface::class, 'sylius_invoicing.custom_factory.invoice');

    $services->set('sylius_invoicing.manager.invoice_file', InvoiceFileManager::class)
        ->args([service('gaufrette.sylius_invoicing_invoice_filesystem')]);

    $services->alias(InvoiceFileManagerInterface::class, 'sylius_invoicing.manager.invoice_file');

    $services->set('sylius_invoicing.provider.invoice_file', InvoiceFileProvider::class)
        ->args([
            service('sylius_invoicing.generator.invoice_file_name'),
            service('gaufrette.sylius_invoicing_invoice_filesystem'),
            service('sylius_invoicing.generator.invoice_pdf_file'),
            service('sylius_invoicing.manager.invoice_file'),
            '%sylius_invoicing.invoice_save_path%',
        ]);

    $services->alias(InvoiceFileProviderInterface::class, 'sylius_invoicing.provider.invoice_file');

    $services->set('sylius_invoicing.provider.unit_net_price', UnitNetPriceProvider::class);

    $services->alias(UnitNetPriceProviderInterface::class, 'sylius_invoicing.provider.unit_net_price');
};
