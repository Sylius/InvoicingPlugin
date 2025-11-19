<?php

declare(strict_types=1);

$bundles = [
    Knp\Bundle\SnappyBundle\KnpSnappyBundle::class => ['all' => true],
    Sylius\InvoicingPlugin\SyliusInvoicingPlugin::class => ['all' => true],
];

if (class_exists('winzou\Bundle\StateMachineBundle\winzouStateMachineBundle')) {
    $bundles[winzou\Bundle\StateMachineBundle\winzouStateMachineBundle::class] = ['all' => true];
}

return $bundles;
