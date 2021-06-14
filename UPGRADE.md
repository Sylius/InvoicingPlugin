### UPGRADE FROM 0.15.0 TO 0.16.0

1. `orderNumber` field on `Sylius\InvoicingPlugin\Entity\Invoice` has been removed and replaced with relation to `Order` entity.
1. `Sylius\InvoicingPlugin\Entity\InvoiceInterface::orderNumber` function is left due to easier and smoother upgrades,
   but is also deprecated and will be removed in the `v1.0.0` release. Use `Sylius\InvoicingPlugin\Entity\InvoiceInterface::order` instead.
1. `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface::findOneByOrderNumber` method has been replaced by
   `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface::findOneByOrder`.
1. `Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface::createForData` takes `OrderInterface $order` as the 3rd argument instead
    of `string $orderNumber`.

### UPGRADE FROM 0.14.0 TO 0.15.0

1. Command bus `sylius_invoicing_plugin.command_bus` has been replaced with `sylius.command_bus`.

1. Event bus `sylius_invoicing_plugin.event_bus` has been replaced with `sylius.event_bus`.

1. Support for Sylius 1.8 has been dropped, upgrade your application to [Sylius 1.9](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.9.md) 
or [Sylius 1.10](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.10.md).

### UPGRADE FROM 0.11.0 TO 0.12.0

1. The custom repository has been removed:

  - the repository class `Sylius\InvoicingPlugin\Repository\DoctrineInvoiceRepository` has been removed 
  and replaced by `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepository`.
  - the related service `sylius_invoicing_plugin.custom_repository.invoice` has been removed,
   use `sylius_invoicing_plugin.repository.invoice` instead
  - the related interface `Sylius\InvoicingPlugin\Repository\InvoiceRepository` has been removed, 
  use `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface` instead.

### UPGRADE FROM 0.10.X TO 0.11.0

1. Upgrade your application to [Sylius 1.8](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.8.md).

1. Remove previously copied migration files (You may check migrations to remove [here](https://github.com/Sylius/InvoicingPlugin/pull/184)).

### UPGRADE FROM 0.9 TO 0.10.0

1. Removed `InvoicingChannel` and replaced by `Sylius\Component\Core\Model\ChannelInterface`.

2. Replaced  `InvoiceShopBillingData` value object by entity with `InvoiceShopBillingDataInterface` interface.
