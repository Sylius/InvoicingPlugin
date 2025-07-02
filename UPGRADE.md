### UPGRADE FROM 0.25 TO 1.0

1. Support for Sylius 1.14 has been added, it is now the recommended Sylius version to use with InvoicingPlugin.

1. Support for Sylius 1.12 has been dropped, upgrade your application to [Sylius 1.13](https://github.com/Sylius/Sylius/blob/1.13/UPGRADE-1.13.md).
   or [Sylius 1.14](https://github.com/Sylius/Sylius/blob/1.14/UPGRADE-1.14.md).

1. The directories structure has been updated to the current Symfony recommendations:
   - `@SyliusInvoicingPlugin/Resources/assets` -> `@SyliusInvoicingPlugin/assets`
   - `@SyliusInvoicingPlugin/Resources/config` -> `@SyliusInvoicingPlugin/config`
   - `@SyliusInvoicingPlugin/Resources/translations` -> `@SyliusInvoicingPlugin/translations`
   - `@SyliusInvoicingPlugin/Resources/views` -> `@SyliusInvoicingPlugin/templates`

   You need to adjust the import of configuration file in your end application:
   ```diff
   imports:
   -   - { resource: "@SyliusInvoicingPlugin/Resources/config/config.yml" }
   +   - { resource: '@SyliusInvoicingPlugin/config/config.yaml' }
   ```

   The routes have been consolidated into a single `config/routes.yaml` file. It is sufficient to import this file instead of defining each route explicitly:
   ```yaml
       sylius_invoicing:
           resource: "@SyliusInvoicingPlugin/config/routes.yaml"
   ```
   
   However, if you have customized the routes, you need to adjust the paths to reflect the new structure:
   ```diff
   -   sylius_invoicing_plugin_admin:
   -       resource: "@SyliusInvoicingPlugin/Resources/config/app/routing/admin_invoicing.yml"
   -       prefix: /admin
   +   sylius_invoicing_admin:
   +       resource: '@SyliusInvoicingPlugin/config/routes/admin.yaml'
   +       prefix: '/%sylius_admin.path_name%'
    
   -   sylius_invoicing_plugin_shop:
   -       resource: "@SyliusInvoicingPlugin/Resources/config/app/routing/shop_invoicing.yml"
   +   sylius_invoicing_shop:
   +       resource: '@SyliusInvoicingPlugin/config/routes/shop.yaml'
           prefix: /{_locale}
           requirements:
               _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
   ```

   And the paths to assets and templates if you are using them.   

1. The deprecated method `Sylius\InvoicingPlugin\Entity\InvoiceInterface::orderNumber()` has been removed, 
   use `Sylius\InvoicingPlugin\Entity\InvoiceInterface::order()` instead.

1. The `Sylius\InvoicingPlugin\SystemDateTimeProvider` class, `Sylius\InvoicingPlugin\DateTimeProvider` interface
   and corresponding `sylius_invoicing_plugin.date_time_provider` service have been removed. 
   It has been replaced by `clock` service and `Symfony\Component\Clock\ClockInterface` interface.

   Affected classes:
   - `Sylius\InvoicingPlugin\Creator\MassInvoicesCreator`
   - `Sylius\InvoicingPlugin\EventProducer\OrderPaymentPaidProducer`
   - `Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer`
   - `Sylius\InvoicingPlugin\Generator\SequentialInvoiceNumberGenerator`

1. The translation keys have been changed from `sylius_invoicing_plugin` and `sylius_admin_order_creation` to `sylius_invoicing`.

1. The `sylius_invoicing_admin_order_show_by_number` route and `Sylius\InvoicingPlugin\Ui\RedirectToOrderShowAction` controller
   have been removed and replaced by the `sylius_admin_order_show` route from the Sylius Core.

1. The following templates have been removed:
   - `@SyliusInvoicingPlugin/Invoice/Grid/Field/orderNumber.html.twig`
   - `@SyliusInvoicingPlugin/Invoice/Grid/Field/channel.html.twig`

1. The custom `invoice_channel` filter, its `Sylius\InvoicingPlugin\Grid\Filter\ChannelFilter` class 
   and `Sylius\InvoicingPlugin\Form\Type\ChannelFilterType` form type have been removed and replaced by 
   the `entity` filter from GridBundle.

1. The invoice grid configuration has been updated accordingly to the above changes:

   ```diff
   sylius_grid:
   -   templates:
   -       filter:
   -           invoice_channel: '@SyliusInvoicingPlugin/Grid/Filter/channel.html.twig'
       grids:
           sylius_invoicing_plugin_invoice:
   #           ...
               fields:
   #               ...
                   orderNumber:
                       type: twig
                       label: sylius.ui.order
   -                   path: order.number
   -                   options:
   -                       template: '@SyliusInvoicingPlugin/Invoice/Grid/Field/orderNumber.html.twig'
   +                   path: order
   +                   options:
   +                       template: "@SyliusAdmin/Order/Grid/Field/number.html.twig"
                       sortable: order.number
                   channel:
                       type: twig
                       label: sylius.ui.channel
                       options:
   -                       template: "@SyliusInvoicingPlugin/Invoice/Grid/Field/channel.html.twig"
   +                       template: "@SyliusAdmin/Order/Grid/Field/channel.html.twig"
               filters:
   #               ...
                   channel:
   -                   type: invoice_channel
   +                   type: entity
                       label: sylius.ui.channel
   +                   form_options:
   +                       class: "%sylius.model.channel.class%"
   #           ...
   ```

1. The naming has been unified throughout the plugin, encompassing the following changes:

#### Configuration root key change

   ```diff
      -   sylius_invoicing_plugin:
      +   sylius_invoicing:
   ```

#### Routing

| Old route                                            | New route                                     |
|------------------------------------------------------|-----------------------------------------------|
| sylius_invoicing_plugin_admin_invoice_index          | sylius_invoicing_admin_invoice_index          |
| sylius_invoicing_plugin_admin_invoice_show           | sylius_invoicing_admin_invoice_show           |
| sylius_invoicing_plugin_admin_invoice_download       | sylius_invoicing_admin_invoice_download       |
| sylius_invoicing_plugin_admin_invoice_resend         | sylius_invoicing_admin_invoice_resend         |
| sylius_invoicing_plugin_admin_order_invoices_partial | sylius_invoicing_admin_order_invoices_partial |
| sylius_invoicing_plugin_shop_invoice_download        | sylius_invoicing_shop_invoice_download        |
| sylius_invoicing_plugin_shop_order_invoices_partial  | sylius_invoicing_shop_order_invoices_partial  |

#### Grids

| Old grid                        | New grid                 |
|---------------------------------|--------------------------|
| sylius_invoicing_plugin_invoice | sylius_invoicing_invoice |

#### Changed Parameters

| Old                                                        | New                                                 |
|------------------------------------------------------------|-----------------------------------------------------|
| default_logo_file                                          | sylius_invoicing.default_logo_file                  |
| sylius.invoicing.template.logo_file                        | sylius_invoicing.template.logo_file                 |
| sylius_invoicing_plugin.controller.billing_data.class      | sylius_invoicing.controller.billing_data.class      |
| sylius_invoicing_plugin.controller.invoice.class           | sylius_invoicing.controller.invoice.class           |
| sylius_invoicing_plugin.controller.invoice_sequence.class  | sylius_invoicing.controller.invoice_sequence.class  |
| sylius_invoicing_plugin.controller.line_item.class         | sylius_invoicing.controller.line_item.class         |
| sylius_invoicing_plugin.controller.shop_billing_data.class | sylius_invoicing.controller.shop_billing_data.class |
| sylius_invoicing_plugin.controller.tax_item.class          | sylius_invoicing.controller.tax_item.class          |
| sylius_invoicing_plugin.factory.billing_data.class         | sylius_invoicing.factory.billing_data.class         |
| sylius_invoicing_plugin.factory.invoice.class              | sylius_invoicing.factory.invoice.class              |
| sylius_invoicing_plugin.factory.invoice_sequence.class     | sylius_invoicing.factory.invoice_sequence.class     |
| sylius_invoicing_plugin.factory.line_item.class            | sylius_invoicing.factory.line_item.class            |
| sylius_invoicing_plugin.factory.shop_billing_data.class    | sylius_invoicing.factory.shop_billing_data.class    |
| sylius_invoicing_plugin.factory.tax_item.class             | sylius_invoicing.factory.tax_item.class             |
| sylius_invoicing_plugin.model.billing_data.class           | sylius_invoicing.model.billing_data.class           |
| sylius_invoicing_plugin.model.invoice.class                | sylius_invoicing.model.invoice.class                |
| sylius_invoicing_plugin.model.invoice_sequence.class       | sylius_invoicing.model.invoice_sequence.class       |
| sylius_invoicing_plugin.model.line_item.class              | sylius_invoicing.model.line_item.class              |
| sylius_invoicing_plugin.model.shop_billing_data.class      | sylius_invoicing.model.shop_billing_data.class      |
| sylius_invoicing_plugin.model.tax_item.class               | sylius_invoicing.model.tax_item.class               |
| sylius_invoicing_plugin.repository.invoice.class           | sylius_invoicing.repository.invoice.class           |

1. Services have been refactored to align with the [New Naming Convention](https://github.com/Sylius/Sylius/blob/2.0/adr/2024_10_03_services_naming_convention.md):

#### Changed Services

| Old id                                                                             | New id                                                                      |
|------------------------------------------------------------------------------------|-----------------------------------------------------------------------------|
| sylius_invoicing_plugin.event_listener.workflow.payment.produce_order_payment_paid | sylius_invoicing.event_listener.workflow.payment.produce_order_payment_paid |
| sylius_invoicing_plugin.ui.action.download_invoice                                 | sylius_invoicing.controller.download_invoice                                |
| sylius_invoicing_plugin.ui.action.resend_invoice                                   | sylius_invoicing.controller.resend_invoice                                  |
| sylius_invoicing_plugin.cli.generate_invoices                                      | sylius_invoicing.cli.generate_invoices                                      |
| sylius_invoicing_plugin.converter.order_item_units_to_line_items                   | sylius_invoicing.converter.order_item_units_to_line_items                   |
| sylius_invoicing_plugin.converter.shipping_adjustments_to_line_items               | sylius_invoicing.converter.shipping_adjustments_to_line_items               |
| sylius_invoicing_plugin.converter.tax_items                                        | sylius_invoicing.converter.tax_items                                        |
| sylius_invoicing_plugin.fixture.example_factory.invoicing_plugin_shop_billing_data | sylius_invoicing.fixture.example_factory.shop_billing_data                  |
| sylius_invoicing_plugin.fixture.shop_billing_data_fixture                          | sylius_invoicing.fixture.shop_billing_data                                  |
| Sylius\InvoicingPlugin\Fixture\Listener\InvoicesPurgerListener                     | sylius_invoicing.fixture.listener.invoices_purger                           |
| Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface                       | sylius_invoicing.provider.invoice_file                                      |
| Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface                         | sylius_invoicing.manager.invoice_file                                       |
| Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface                      | sylius_invoicing.provider.unit_net_price                                    |
| Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface                 | sylius_invoicing.generator.invoice_file_name                                |
| sylius_invoicing_plugin.generator.invoice_number_generator                         | sylius_invoicing.generator.invoice_number                                   |
| sylius_invoicing_plugin.generator.invoice_identifier                               | sylius_invoicing.generator.invoice_identifier                               |
| sylius_invoicing_plugin.generator.invoice                                          | sylius_invoicing.generator.invoice                                          |
| sylius_invoicing_plugin.generator.invoice_pdf_file                                 | sylius_invoicing.generator.invoice_pdf_file                                 |
| sylius_invoicing_plugin.creator.invoice                                            | sylius_invoicing.creator.invoice                                            |
| sylius_invoicing_plugin.creator.mass_invoices                                      | sylius_invoicing.creator.mass_invoices                                      |
| sylius_invoicing_plugin.generator.pdf_options                                      | sylius_invoicing.generator.pdf_options                                      |
| sylius_invoicing_plugin.generator.twig_to_pdf                                      | sylius_invoicing.generator.twig_to_pdf                                      |
| sylius_invoicing_plugin.event_producer.order_payment_paid                          | sylius_invoicing.event_producer.order_payment_paid                          |
| sylius_invoicing_plugin.event_listener.order_placed                                | sylius_invoicing.listener.order_placed                                      |
| sylius_invoicing_plugin.event_producer.order_placed                                | sylius_invoicing.event_producer.order_placed                                |
| sylius_invoicing_plugin.listener.order_payment_paid                                | sylius_invoicing.listener.order_payment_paid                                |
| sylius_invoicing_plugin.ui.menu.admin_menu_listener                                | sylius_invoicing.listener.admin_menu                                        |
| sylius_invoicing_plugin.email.invoice_email_sender                                 | sylius_invoicing.email.invoice_email_sender                                 |
| sylius_invoicing_plugin.command_handler.send_invoice_email                         | sylius_invoicing.command_handler.send_invoice_email                         |
| sylius_invoicing_plugin.provider.tax_rate_percentage                               | sylius_invoicing.provider.tax_rate_percentage                               |
| sylius_invoicing_plugin.custom_factory.invoice                                     | sylius_invoicing.custom_factory.invoice                                     |
| sylius_invoicing_plugin_security.voter.invoice                                     | sylius_invoicing.security.voter.invoice                                     |
| sylius_invoicing_plugin.controller.invoice                                         | sylius_invoicing.controller.invoice                                         |
| sylius_invoicing_plugin.repository.invoice                                         | sylius_invoicing.repository.invoice                                         |
| sylius_invoicing.factory.invoice                                                   | sylius_invoicing.factory.invoice                                            |
| sylius_invoicing_plugin.controller.billing_data                                    | sylius_invoicing.controller.billing_data                                    |
| sylius_invoicing_plugin.repository.billing_data                                    | sylius_invoicing.repository.billing_data                                    |
| sylius_invoicing_plugin.factory.billing_data                                       | sylius_invoicing.factory.billing_data                                       |
| sylius_invoicing_plugin.controller.shop_billing_data                               | sylius_invoicing.controller.shop_billing_data                               |
| sylius_invoicing_plugin.repository.shop_billing_data                               | sylius_invoicing.repository.shop_billing_data                               |
| sylius_invoicing_plugin.factory.shop_billing_data                                  | sylius_invoicing.factory.shop_billing_data                                  |
| sylius_invoicing_plugin.controller.line_item                                       | sylius_invoicing.controller.line_item                                       |
| sylius_invoicing_plugin.repository.line_item                                       | sylius_invoicing.repository.line_item                                       |
| sylius_invoicing_plugin.factory.line_item                                          | sylius_invoicing.factory.line_item                                          |
| sylius_invoicing_plugin.controller.tax_item                                        | sylius_invoicing.controller.tax_item                                        |
| sylius_invoicing_plugin.repository.tax_item                                        | sylius_invoicing.repository.tax_item                                        |
| sylius_invoicing_plugin.factory.tax_item                                           | sylius_invoicing.factory.tax_item                                           |
| sylius_invoicing_plugin.controller.invoice_sequence                                | sylius_invoicing.controller.invoice_sequence                                |
| sylius_invoicing_plugin.repository.invoice_sequence                                | sylius_invoicing.repository.invoice_sequence                                |
| sylius_invoicing_plugin.factory.invoice_sequence                                   | sylius_invoicing.factory.invoice_sequence                                   |
| sylius_invoicing_plugin.manager.invoice                                            | sylius_invoicing.manager.invoice                                            |
| sylius_invoicing_plugin.manager.billing_data                                       | sylius_invoicing.manager.billing_data                                       |
| sylius_invoicing_plugin.manager.shop_billing_data                                  | sylius_invoicing.manager.shop_billing_data                                  |
| sylius_invoicing_plugin.manager.line_item                                          | sylius_invoicing.manager.line_item                                          |
| sylius_invoicing_plugin.manager.tax_item                                           | sylius_invoicing.manager.tax_item                                           |
| sylius_invoicing_plugin.manager.invoice_sequence                                   | sylius_invoicing.manager.invoice_sequence                                   |
| sylius_invoicing_plugin.controller_state_machine.invoice                           | sylius_invoicing.controller_state_machine.invoice                           |
| sylius_invoicing_plugin.controller_state_machine.billing_data                      | sylius_invoicing.controller_state_machine.billing_data                      |
| sylius_invoicing_plugin.controller_state_machine.shop_billing_data                 | sylius_invoicing.controller_state_machine.shop_billing_data                 |
| sylius_invoicing_plugin.controller_state_machine.line_item                         | sylius_invoicing.controller_state_machine.line_item                         |
| sylius_invoicing_plugin.controller_state_machine.tax_item                          | sylius_invoicing.controller_state_machine.tax_item                          |
| sylius_invoicing_plugin.controller_state_machine.invoice_sequence                  | sylius_invoicing.controller_state_machine.invoice_sequence                  |

1. The visibility of services has been changed to `private` by default. This change enhances the performance
   and maintainability of the application and also follows Symfony's best practices for service encapsulation.

   Exceptions:
   - Services required by Symfony to be `public` (e.g., controllers, event listeners) remain public.
