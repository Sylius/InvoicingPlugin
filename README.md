# InvoicingPlugin

SyliusInvoicingPlugin creates new immutable invoice once an order is placed and allows
both customer and admin to download invoices related to the order.   

## Installation

Require plugin with composer:

```bash
composer require sylius/invoicing-plugin
```

Import routing:

```yaml
imports:
    - { resource: "@SyliusInvoicingPlugin/Resources/config/app/routing.yml" }
```

Add plugin class to your `AppKernel`:

```php
$bundles = [
    new \Sylius\InvoicingPlugin\SyliusInvoicingPlugin(),
];
```

Clear cache:

```bash
bin/console cache:clear
```
