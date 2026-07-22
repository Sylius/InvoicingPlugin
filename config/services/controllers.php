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

use Sylius\InvoicingPlugin\Ui\Action\Admin\ResendInvoiceAction;
use Sylius\InvoicingPlugin\Ui\Action\DownloadInvoiceAction;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_invoicing.controller.download_invoice', DownloadInvoiceAction::class)
        ->args([
            service('sylius_invoicing.repository.invoice'),
            service('security.authorization_checker'),
            service('sylius_invoicing.provider.invoice_file'),
            '%sylius_invoicing.pdf_generator.enabled%',
        ]);

    $services->set('sylius_invoicing.controller.resend_invoice', ResendInvoiceAction::class)
        ->args([
            service('sylius_invoicing.repository.invoice'),
            service('sylius_invoicing.email.invoice_email_sender'),
            service('sylius.repository.order'),
            service('router'),
            service('request_stack'),
        ]);
};
