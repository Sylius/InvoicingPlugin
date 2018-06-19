<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusInvoicingExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependWinzouStateMachine($container);
        $this->prependProophServiceBus($container);
    }

    private function prependWinzouStateMachine(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('winzou_state_machine')) {
            throw new \RuntimeException('WinzouStateMachineBundle must be registered in kernel.');
        }

        $container->prependExtensionConfig('winzou_state_machine', [
            'sylius_order' => [
                'callbacks' => [
                    'after' => [
                        'sylius_invoicing_plugin_order_created_producer' => [
                            'on' => ['create'],
                            'do' => ['@Sylius\InvoicingPlugin\EventListener\OrderPlacedProducer', '__invoke'],
                            'args' => ['object'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function prependProophServiceBus(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('prooph_service_bus')) {
            throw new \RuntimeException('ProophServiceBusBundle must be registered in kernel.');
        }

        $container->prependExtensionConfig('prooph_service_bus', [
            'event_buses' => [
                'sylius_invoicing_event_bus' => null,
            ],
        ]);
    }
}
