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

use Sylius\InvoicingPlugin\EventListener\CreateInvoiceOnOrderPlacedListener;
use Sylius\InvoicingPlugin\EventListener\OrderPaymentPaidListener;
use Sylius\InvoicingPlugin\EventProducer\OrderPaymentPaidProducer;
use Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_invoicing.event_producer.order_payment_paid', OrderPaymentPaidProducer::class)
        ->args([
            service('sylius.event_bus'),
            service('clock'),
            service('sylius_invoicing.repository.invoice'),
        ]);

    $services->set('sylius_invoicing.listener.order_placed', CreateInvoiceOnOrderPlacedListener::class)
        ->args([service('sylius_invoicing.creator.invoice')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius_invoicing.event_producer.order_placed', OrderPlacedProducer::class)
        ->args([
            service('sylius.event_bus'),
            service('clock'),
        ])
        ->tag('doctrine.event_listener', ['event' => 'postPersist'])
        ->tag('doctrine.event_listener', ['event' => 'postUpdate']);

    $services->set('sylius_invoicing.listener.order_payment_paid', OrderPaymentPaidListener::class)
        ->args([service('sylius.command_bus')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);
};
