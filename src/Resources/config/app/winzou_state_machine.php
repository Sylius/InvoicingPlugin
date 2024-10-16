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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use winzou\Bundle\StateMachineBundle\winzouStateMachineBundle;

return static function (ContainerConfigurator $container) {
    if (class_exists(winzouStateMachineBundle::class)) {
        $container->extension('winzou_state_machine', [
            'sylius_payment' => [
                'callbacks' => [
                    'after' => [
                        'sylius_invoicing_plugin_payment_complete_producer' => [
                            'on' => ['complete'],
                            'do' => ['@sylius_invoicing_plugin.event_producer.order_payment_paid', '__invoke'],
                            'args' => ['object'],
                        ],
                    ],
                ],
            ],
        ]);
    }
};
