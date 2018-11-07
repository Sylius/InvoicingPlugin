### Fixtures configuration instructions

1. Add a new yaml file to the folder `config/packages` and name it `my_own_company_fixtures.yaml`

2. Fill this yaml with your own company fixtures and don't forget to put first the definition of
   your channel(s)
   
   You must define the channel which you will use before the definition of `invoicing_plugin_shop_billing_data`.
   
   ```yaml
   sylius_fixtures:
       suites:
           my_own_company_fixtures:
   
               listeners:
                   orm_purger: ~
                   logger: ~
   
               fixtures:
   
                   channel:
                       options:
                           custom:
                               default_web_store:
                                   enabled: true
                                   name: "Web Store"
                                   code: "default"
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
    php bin/console -vvv sylius:fixture:load my_own_company_fixtures
    ```