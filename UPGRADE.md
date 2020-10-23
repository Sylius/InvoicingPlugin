### UPGRADE FROM 0.11.0 TO 0.11.1

1. The custom repository has been removed :

  - removed the repository class `DoctrineInvoiceRepository` and replaced by `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepository`
  - removed the related service `sylius_invoicing_plugin.custom_repository.invoice` use `sylius_invoicing_plugin.repository.invoice` instead
  - removed the related interface `InvoiceRepository` use `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface` instead

### UPGRADE FROM 0.10.X TO 0.11.0

1. Upgrade your application to [Sylius 1.8](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.8.md).

1. Remove previously copied migration files (You may check migrations to remove [here](https://github.com/Sylius/InvoicingPlugin/pull/184)).

### UPGRADE FROM 0.9 TO 0.10.0

1. Removed `InvoicingChannel` and replaced by `Sylius\Component\Core\Model\ChannelInterface`.

2. Replaced  `InvoiceShopBillingData` value object by entity with `InvoiceShopBillingDataInterface` interface.
