<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Invoicing Plugin</h1>

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This plugin creates an invoice related to the order.</p>

SyliusInvoicingPlugin creates new immutable invoice when the order is in given state (default: created) and allows
both customer and admin to download invoices related to the order.

![Screenshot showing invoice](docs/screenshot_invoice.png)
![Screenshot showing invoice browsing page in administration panel](docs/screenshot_admin.png)

## Business value

The primary aim of Invoicing Plugin is to create a document representing Customer's will to buy particular products and 
pay for them.

An Invoice can also be treated as a proof of placing an Order. Thus, it is downloadable as .pdf file and can be sent to 
Customer manually by the Administrator or automatically once an Order is paid.

Additional feature of the plugin that fulfills Invoicing domain is the ability to set billing data on a Seller.

## Installation

1. Require plugin with composer:

    ```bash
    composer require sylius/invoicing-plugin
    ```
    
    > Remember to allow community recipes with `composer config extra.symfony.allow-contrib true` or during plugin installation process

2. Copy plugin migrations to your migrations directory (e.g. `src/Migrations`) and apply them to your database:

    ```bash
    cp -R vendor/sylius/invoicing-plugin/migrations/* src/Migrations
    bin/console doctrine:migrations:migrate
    ```

3. Check if you have wkhtmltopdf binary. If not, you can download it [here](https://wkhtmltopdf.org/downloads.html).
   
In case wkhtmltopdf is not located in /usr/local/bin/wkhtmltopdf, add a following snippet at the end of your application's config.yml:

```
knp_snappy:
    pdf:
        enabled: true
        binary: /usr/local/bin/wkhtmltopdf # Change this! :)
        options: []
```

4. If you want to generate invoices for orders placed before plugin's installation run the following command using your terminal:

```bash
bin/console sylius-invoicing:generate-invoices
```

#### Beware!

This installation instruction assumes that you're using Symfony Flex. If you don't, take a look at the
[legacy installation instruction](docs/legacy_installation.md). However, we strongly encourage you to use
Symfony Flex, it's much quicker! :)

## Extension points

Majority of actions contained in SyliusInvoicingPlugin is executed once an event after changing the state of
the Order on `winzou_state_machine` is dispatched.

Here is the example:

```yaml
winzou_state_machine:
    sylius_payment:
        callbacks:
            after:
                sylius_invoicing_plugin_payment_complete_producer:
                    on: ['complete']
                    do: ['@sylius_invoicing_plugin.event_producer.order_payment_paid', '__invoke']
                    args: ['object']
```

Code placed above is a part of configuration placed in `config.yml` file.
You can customize this file by adding new state machine events listeners or editing existing ones.

Apart from that an Invoice model is treated as a Resource.

You can read more about Resources here:

<http://docs.sylius.com/en/latest/components_and_bundles/bundles/SyliusResourceBundle/index.html>.

Hence, template for displaying the list of Invoices is defined in `routing.yml` file:

```yaml
sylius_invoicing_plugin_invoice:
    resource: |
        alias: sylius_invoicing_plugin.invoice
        section: admin
        templates: SyliusAdminBundle:Crud
        only: ['index']
        grid: sylius_invoicing_plugin_invoice
        permission: true
        vars:
            all:
                subheader: sylius_invoicing_plugin.ui.manage_invoices
            index:
                icon: inbox
    type: sylius.resource
```

Another aspect that can be both replaced and customized is displaying Invoices list on Order show view.
Code responsible for displaying Invoices related to the Order is injected to existing Sylius template using
Sonata events. You can read about customizing templates via events here:

<http://docs.sylius.com/en/latest/customization/template.html>

Invoicing Plugin renders invoices grid using a certain pattern, including displaying a dot next to channel in which an invoice as issued.
The dot's color is dependant on a property defined on Channel entity or, if not provided, a global parameter named `default_channel_color`.

Like any other parameter, `default_channel_color` can also be overwritten in your `config.yml` file.

## Using different filesystem adapters

You can change the adapter used by the invoice filesystem to generate your PDF files directly in AwsS3, AzureBlobStorage, SFTP, etc.

First you need to install the corresponding adapter for your filesystem via `composer require`. Go to [this list](https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/index.md#step-1-download-the-bundle) on the OneUpFlysystemBundle repository to see the available adapters and follow the links to the adapter configurations in [this section](https://github.com/1up-lab/OneupFlysystemBundle/blob/master/Resources/doc/index.md#step3-configure-your-filesystems).

If you want your PDF files to be stored in a local directory you can leverage the `LocalAdapter` with the following adapter configuration in your `oneup_flysystem.yaml` without having to install an additional adapter:

```
oneup_flysystem:
    adapters:
        invoice:
            local:
                directory: '%kernel.project_dir%/var/storage/invoice'

    filesystems:
        invoice:
            adapter: invoice
            alias: League\Flysystem\Filesystem
```

## Fixtures

You can add `ShopBillingData` fixtures into a yaml to add Channel ShopBillingData info to your installation.
More instructions on the [fixtures configuration instructions](docs/fixtures.md).

## Security issues

If you think that you have found a security issue, please do not use the issue tracker and do not post it publicly. 
Instead, all security issues must be sent to `security@sylius.com`.
