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

use Sylius\InvoicingPlugin\Cli\GenerateInvoicesCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_invoicing.cli.generate_invoices', GenerateInvoicesCommand::class)
        ->args([
            service('sylius_invoicing.creator.mass_invoices'),
            service('sylius.repository.order'),
        ])
        ->tag('console.command');
};
