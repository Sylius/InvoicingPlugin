### Fixtures configuration instructions

1. Add a new yaml file to the folder `config/packages` and name it as you wish, e.g. `my_own_company_fixtures.yaml`.

2. Fill this yaml with your own company fixtures and don't forget to put first the definition of
   your channel(s) or use an existing one.
   
   ```yaml
   sylius_fixtures:
       suites:
           my_own_company_fixtures:
               fixtures:
                   channel:
                       options:
                           custom:
                               default_web_store:
                                   enabled: true
                                   name: "Web Store"
                                   code: "DEFAULT"
                                   contact_email: "support@exemple.com"
                                   default_locale: "%locale%"
                                   hostname: null
                                   locales:
                                       - "en_US"
                                       - "fr_FR"
                                       - "es_ES"
                                       - "de_DE"
                                   base_currency: "USD"
                                   currencies:
                                       - "USD"
                                       - "EUR"
                   invoicing_plugin_shop_billing_data:
                       options:
                           custom:
                               default_web_store:
                                   channel_code: 'default'
                                   company: 'My company'
                                   country_code: 'US'
                                   city: 'My city'
                                   postcode: '00000'
                                   tax_id: '1234567890'
                                   street_address: '1 my street address'
       ```
    
 3. Load your fixtures
 
    ```bash
    php bin/console sylius:fixture:load my_own_company_fixtures
    ```
