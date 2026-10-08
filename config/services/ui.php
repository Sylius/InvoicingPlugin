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

use Sylius\InvoicingPlugin\Ui\Menu\AdminMenuListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_invoicing.listener.admin_menu ', AdminMenuListener::class)
        ->tag('kernel.event_listener', ['event' => 'sylius.menu.admin.main', 'method' => '__invoke']);
};
