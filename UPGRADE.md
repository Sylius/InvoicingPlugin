### UPGRADE FROM 0.12.0 TO 0.13.0

1. Due to compatibility with Symfony 5:

  - Add `Twig` to Composer dependencies
  - increase Symfony dependencies in Composer to `^5.1`
  - replace template engine service `Symfony\Bundle\FrameworkBundle\Templating\EngineInterface` to `Twig\Environment` in `Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator`
  
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
