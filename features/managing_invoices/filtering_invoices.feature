@managing_invoices
Feature: Filtering invoices
    In order to browse invoices more efficiently
    As an Administrator
    I want to be able to filter invoices list

    Background:
        Given the store operates on a channel named "WEB-DE" in "EUR" currency
        And the store operates on a channel named "WEB-FR" in "EUR" currency
        And the store has country "Germany"
        And the store has country "France"
        And the store has a zone "Germany + France" with code "DE + FR"
        And this zone has the "Germany" country member
        And this zone has the "France" country member
        And the store has a product "PHP T-Shirt" priced at "€10" available in channel "WEB-DE" and channel "WEB-FR"
        And the store ships everywhere for free for all channels
        And the store allows paying offline for all channels
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And this order has been placed in "WEB-DE" channel
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "Germany" with "Offline" payment
        And there is a customer "jane.doe@gmail.com" that placed an order "#00000023"
        And this order has been placed in "WEB-FR" channel
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "France" with "Offline" payment
        And I am logged in as an administrator

    @ui
    Scenario: Filtering credit memos by channel
        When I browse invoices
        And I filter invoices by "WEB-DE" channel
        Then there should be 1 invoice generated
        And I should see a single invoice for order "#00000022"
