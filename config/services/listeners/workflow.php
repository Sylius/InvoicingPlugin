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

use Sylius\InvoicingPlugin\EventListener\Workflow\Payment\ProduceOrderPaymentPaidListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_invoicing.event_listener.workflow.payment.produce_order_payment_paid', ProduceOrderPaymentPaidListener::class)
        ->args([service('sylius_invoicing.event_producer.order_payment_paid')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.complete', 'priority' => 50]);
};
