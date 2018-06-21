<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusInvoicingExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius_invoicing_plugin', 'doctrine/orm', $config['resources'], $container);

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependWinzouStateMachine($container);
        $this->prependProophServiceBus($container);
        $this->prependSyliusGrid($container);
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

    /**
     * @param ContainerBuilder $container
     */
    private function prependSyliusGrid(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('sylius_grid')) {
            throw new \RuntimeException('SyliusGridBundle must be registered in kernel.');
        }

        $container->prependExtensionConfig('sylius_grid', [
            'grids' => [
                'sylius_invoicing_plugin_invoice' => [
                    'driver' => [
                        'name' => 'doctrine/orm',
                        'options' => ['class' => Invoice::class], // TODO: Use parameter "sylius_invoicing_plugin.model.invoice.class" instead of class
                    ],
                    'sorting' => [
                        'issuedAt' => 'desc',
                    ],
                    'fields' => [
                        'id' => [
                            'type' => 'string',
                            'label' => 'sylius_invoicing_plugin.invoice_id',
                            'sortable' => true,
                        ],
                        'orderNumber' => [
                            'type' => 'string',
                            'label' => 'sylius_invoicing_plugin.order_number',
                            'sortable' => true,
                        ],
                        'issuedAt' => [
                            'type' => 'datetime',
                            'label' => 'sylius_invoicing_plugin.issued_at',
                            'sortable' => true,
                        ],
                    ],
                    'filters' => [
                        'id' => [
                            'type' => 'string',
                            'label' => 'sylius_invoicing_plugin.invoice_id',
                        ],
                        'orderNumber' => [
                            'type' => 'string',
                            'label' => 'sylius_invoicing_plugin.order_number',
                        ],
                    ],
                    'actions' => [
                        'item' => [
                            'show' => [
                                'type' => 'show',
                            ],
                            'download' => [
                                'type' => 'show',
                                'label' => 'sylius_invoicing_plugin.download_invoice',
                                'options' => [
                                    'link' => [
                                        'route' => 'sylius_invoicing_plugin_admin_invoice_download',
                                        'parameters' => ['id' => 'resource.id'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
