### Legacy installation (without Symfony Flex)

1. Require plugin with composer:

    ```bash
    composer require sylius/invoicing-plugin
    ```
    
2. Add plugin class to your `AppKernel`:

    ```php
    $bundles = [
        new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
        new \Prooph\Bundle\ServiceBus\ProophServiceBusBundle(),
        new \Sylius\InvoicingPlugin\SyliusInvoicingPlugin(),
    ];
    ```

3. Import configuration:

    ```yaml
    imports:
        - { resource: "@SyliusInvoicingPlugin/Resources/config/config.yml" }
    ```

4. Import routing:

    ```yaml
    sylius_invoicing_plugin_admin:
        resource: "@SyliusInvoicingPlugin/Resources/config/app/routing/admin_invoicing.yml"
        prefix: /admin
    
    sylius_invoicing_plugin_shop:
        resource: "@SyliusInvoicingPlugin/Resources/config/app/routing/shop_invoicing.yml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$
    ```

5. Check if you have `wkhtmltopdf` binary. If not, you can download it [here](https://wkhtmltopdf.org/downloads.html).

    In case `wkhtmltopdf` is not located in `/usr/local/bin/wkhtmltopdf`, add a following snippet at the end of your application's `config.yml`:
    
    ```yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: /usr/local/bin/wkhtmltopdf # Change this! :)
            options: []
    ```   

2. Copy plugin migrations to your migrations directory (e.g. `src/Migrations`) and apply them to your database:

    ```bash
    cp -R vendor/sylius/invoicing-plugin/migrations/* src/Migrations
    bin/console doctrine:migrations:migrate
    ```

7. Override Channel entity:

    a) Write new class which will use `ShopBillingDataTrait` and implement `ShopBillingDataAwareInterface`:

    ```php
    <?php
 
    namespace AppBundle\Entity;
 
    use Doctrine\ORM\Mapping\MappedSuperclass;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Component\Core\Model\Channel as BaseChannel;
    use Sylius\InvoicingPlugin\Entity\ShopBillingDataAwareInterface;
    use Sylius\InvoicingPlugin\Entity\ShopBillingDataTrait;
    
    /**
     * @MappedSuperclass
     * @Table(name="sylius_channel")
     */
    class Channel extends BaseChannel implements ShopBillingDataAwareInterface
    {
        use ShopBillingDataTrait;
    }
    
    ```

    b) And override the model's class in the `app/config/config.yml`:

    ```yaml
    sylius_channel:
        resources:
            channel:
                classes:
                    model: AppBundle\Entity\Channel
    ```

8. Clear cache:

    ```bash
    bin/console cache:clear
    ```
