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

use Sylius\InvoicingPlugin\Twig\Component\Invoice\ListComponent;
use Sylius\InvoicingPlugin\Twig\InvoicesExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_invoicing.twig.extension.invoices', InvoicesExtension::class)
        ->args([
            service('sylius_invoicing.repository.invoice'),
            '%sylius_invoicing.pdf_generator.enabled%',
        ])
        ->tag('twig.extension');

    $services->set('sylius_invoicing.twig.component.invoice.list', ListComponent::class)
        ->args([service('sylius_invoicing.repository.invoice')])
        ->tag('sylius.twig_component', ['key' => 'sylius_invoicing:invoice:list', 'template' => '@SyliusInvoicingPlugin/shared/components/invoices.html.twig']);
};
