### UPGRADE FROM 0.20.0 TO 0.21.0

1. `Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator` takes `Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface` in place of `Twig\Environment` and `Knp\Snappy\GeneratorInterface`.

### UPGRADE FROM 0.19.0 TO 0.20.0

1. Since 0.20.0, the recommended Sylius version to use with InvoicingPlugin is `1.11.*`. If you would like to upgrade Sylius to v1.11.0,
   please follow [Sylius's upgrade instructions](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.11.md)
   and [Sylius API's upgrade instructions](https://github.com/Sylius/Sylius/blob/master/UPGRADE-API-1.11.md).

### UPGRADE FROM 0.18.0 TO 0.19.0

1. Support for Sylius 1.9 has been dropped, upgrade your application to [Sylius 1.10](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.10.md).
2. Support for Symfony 5.2 and 5.3 has been dropped.
3. Default channel color feature was removed in favour of `sylius/sylius` standard solution.
Override `src/Sylius/Bundle/AdminBundle/Resources/views/Common/_channel.html.twig` if custom default color is neccesasary.
    - Class `Sylius\InvoicingPlugin\Provider\ChannelColorProvider` was removed.
    - Interface `Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface` was removed.
    - Class `Sylius\InvoicingPlugin\Twig\ChannelColorExtension` and twig filter `sylius_channel_color` were removed.
    - Parameter `default_channel_color` was removed

### UPGRADE FROM 0.17.0 TO 0.18.0

1. The `Sylius\InvoicingPlugin\Converter\BillingDataConverter` has been removed in favor of `Sylius\InvoicingPlugin\Factory\BillingDataFactory->createFromAddress`.
2. The `Sylius\InvoicingPlugin\Converter\InvoiceShopBillingDataConverter` has been removed in favor of `Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactory->createFromChannel`.

Now on invoice admin and shop user can check if related order was paid before invoice generated.

1. `src/Entity/Invoice.php` model has new field (`paymentState`), and updated constructor arguments:

    ```dif
        public function __construct(
            string $id,
            string $number,
            OrderInterface $order,
            \DateTimeInterface $issuedAt,
            BillingDataInterface $billingData,
            string $currencyCode,
            string $localeCode,
            int $total,
            Collection $lineItems,
            Collection $taxItems,
            ChannelInterface $channel,
    +       string $paymentState,
            InvoiceShopBillingDataInterface $shopBillingData
        ) {
            $this->id = $id;
            $this->number = $number;
            $this->order = $order;
            $this->issuedAt = clone $issuedAt;
            $this->billingData = $billingData;
            $this->currencyCode = $currencyCode;
            $this->localeCode = $localeCode;
            $this->total = $total;
            $this->lineItems = $lineItems;
            $this->taxItems = $taxItems;
            $this->channel = $channel;
    +       $this->paymentState = $paymentState;
            $this->shopBillingData = $shopBillingData;
   ```

2. New field on `src/Entity/Invoice.php` implies a database update

3. Method `createForData` from `src/Factory/InvoiceFactory.php` service was updated:

    ```dif
        public function createForData(
            string $id,
            string $number,
            OrderInterface $order,
            \DateTimeInterface $issuedAt,
            BillingDataInterface $billingData,
            string $currencyCode,
            string $localeCode,
            int $total,
            Collection $lineItems,
            Collection $taxItems,
            ChannelInterface $channel,
    +       string $paymentState,
            InvoiceShopBillingDataInterface $shopBillingData = null
        ): InvoiceInterface {
            // ...
        }
    ```

### UPGRADE FROM 0.16.1 TO 0.17.0

Invoices are now saved on the server during their generation (by default, when the order is paid).

1. `Sylius\InvoicingPlugin\Creator\InvoiceCreator` class has 2 more dependencies: `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    and `InvoiceFileManagerInterface $invoiceFileManager`
2. `Sylius\InvoicingPlugin\Email\InvoiceEmailSender` class 2nd dependency has been changed from `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    to `InvoiceFileProviderInterface $invoiceFileProvider`
3. `Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator` class has additional `InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator`
    dependency, placed on 4th place, before `string $template`
4. `Sylius\InvoicingPlugin\Ui\Action\DownloadInvoiceAction` class 4th dependency has been changed from `InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator`
    to `InvoiceFileProviderInterface $invoiceFilePathProvider`
5. `Sylius\InvoicingPlugin\Converter\LineItemsConverter` class has additional `TaxRatePercentageProviderInterface $taxRatePercentageProvider`
   dependency
6. `Sylius\InvoicingPlugin\Provider\TaxRateProvider` service has been changed to `Sylius\InvoicingPlugin\Provider\TaxRatePercentageProvider`
   and its service definition from `sylius_invoicing_plugin.provider.tax_rate` to `sylius_invoicing_plugin.provider.tax_rate_percentage`
7. `Sylius\InvoicingPlugin\Converter\LineItemsConverter` service has been replaced by `Sylius\InvoicingPlugin\Converter\OrderItemUnitsToLineItemsConverter`
   and `Sylius\InvoicingPlugin\Converter\ShippingAdjustmentsToLineItemsConverter`
8. `Sylius\InvoicingPlugin\Generator\InvoiceGenerator` class has 2 more dependencies: `LineItemsConverterInterface $orderItemUnitsToLineItemsConverter`
   and `LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter` that replaced `LineItemsConverterInterface $lineItemsConverter`
9. The return type of `Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface:convert` method has been changed
   from `Collection` to `array`
10. `Sylius\InvoicingPlugin\Filesystem\TemporaryFilesystem` class has been removed

### UPGRADE FROM 0.15.0 TO 0.16.0

1. `orderNumber` field on `Sylius\InvoicingPlugin\Entity\Invoice` has been removed and replaced with relation to `Order` entity.
2. `Sylius\InvoicingPlugin\Entity\InvoiceInterface::orderNumber` function is left due to easier and smoother upgrades,
   but is also deprecated and will be removed in the `v1.0.0` release. Use `Sylius\InvoicingPlugin\Entity\InvoiceInterface::order` instead.
3. `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface::findOneByOrderNumber` method has been replaced by
   `Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface::findOneByOrder`.
4. `Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface::createForData` takes `OrderInterface $order` as the 3rd argument instead
    of `string $orderNumber`.

### UPGRADE FROM 0.14.0 TO 0.15.0

1. Command bus `sylius_invoicing_plugin.command_bus` has been replaced with `sylius.command_bus`.

2. Event bus `sylius_invoicing_plugin.event_bus` has been replaced with `sylius.event_bus`.

3. Support for Sylius 1.8 has been dropped, upgrade your application to [Sylius 1.9](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.9.md)
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

2. Remove previously copied migration files (You may check migrations to remove [here](https://github.com/Sylius/InvoicingPlugin/pull/184)).

### UPGRADE FROM 0.9 TO 0.10.0

1. Removed `InvoicingChannel` and replaced by `Sylius\Component\Core\Model\ChannelInterface`.

2. Replaced  `InvoiceShopBillingData` value object by entity with `InvoiceShopBillingDataInterface` interface.
