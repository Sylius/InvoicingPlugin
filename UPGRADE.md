### UPGRADE FROM 0.17.0 TO 0.18.0

1. The getter methods of `Sylius\InvoicingPlugin\Entity\Invoice` are now deprecated and will be replaced with:

    ```diff
    -   public function id(): ?int
    +   public function getId()
    -   public function number(): string
    +   public function getNumber(): string
    -   public function order(): OrderInterface
    +   public function getOrder(): OrderInterface
    -   public function issuedAt(): \DateTimeInterface
    +   public function getIssuedAt(): \DateTimeInterface
    -   public function billingData(): BillingDataInterface
    +   public function getBillingData(): BillingDataInterface
    -   public function currencyCode(): string
    +   public function getCurrencyCode(): string
    -   public function localeCode(): string
    +   public function getLocaleCode(): string
    -   public function total(): int
    +   public function getTotal(): int
    -   public function lineItems(): Collection
    +   public function getLineItems(): Collection
    -   public function taxItems(): Collection
    +   public function getTaxItems(): Collection
    -   public function subtotal(): int
    +   public function getSubtotal(): int
    -   public function taxesTotal(): int
    +   public function getTaxesTotal(): int
    -   public function channel(): ChannelInterface
    +   public function getChannel(): ChannelInterface
    -   public function shopBillingData(): InvoiceShopBillingDataInterface
    +   public function getShopBillingData(): InvoiceShopBillingDataInterface
   ```

### UPGRADE FROM 0.16.1 TO 0.17.0

Invoices are now saved on the server during their generation (by default, when the order is paid).

1. `Sylius\InvoicingPlugin\Creator\InvoiceCreator` class has 2 more dependencies: `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    and `InvoiceFileManagerInterface $invoiceFileManager`
1. `Sylius\InvoicingPlugin\Email\InvoiceEmailSender` class 2nd dependency has been changed from `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    to `InvoiceFileProviderInterface $invoiceFileProvider`
1. `Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator` class has additional `InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator`
    dependency, placed on 4th place, before `string $template`
1. `Sylius\InvoicingPlugin\Ui\Action\DownloadInvoiceAction` class 4th dependency has been changed from `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    to `InvoiceFileProviderInterface $invoiceFilePathProvider`
1. `Sylius\InvoicingPlugin\Converter\LineItemsConverter` class has additional `TaxRatePercentageProviderInterface $taxRatePercentageProvider`
   dependency
1. `Sylius\InvoicingPlugin\Provider\TaxRateProvider` service has been changed to `Sylius\InvoicingPlugin\Provider\TaxRatePercentageProvider`
   and its service definition from `sylius_invoicing_plugin.provider.tax_rate` to `sylius_invoicing_plugin.provider.tax_rate_percentage`
1. `Sylius\InvoicingPlugin\Converter\LineItemsConverter` service has been replaced by `Sylius\InvoicingPlugin\Converter\OrderItemUnitsToLineItemsConverter`
   and `Sylius\InvoicingPlugin\Converter\ShippingAdjustmentsToLineItemsConverter`
1. `Sylius\InvoicingPlugin\Generator\InvoiceGenerator` class has 2 more dependencies: `LineItemsConverterInterface $orderItemUnitsToLineItemsConverter`
   and `LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter` that replaced `LineItemsConverterInterface $lineItemsConverter`
1. The return type of `Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface:convert` method has been changed 
   from `Collection` to `array`
1. `Sylius\InvoicingPlugin\Filesystem\TemporaryFilesystem` class has been removed

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
