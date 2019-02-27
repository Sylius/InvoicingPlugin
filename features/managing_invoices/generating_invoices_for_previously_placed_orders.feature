@managing_invoices @cli
Feature: Generating invoices for previously placed orders
    In order to make my shop consistent with a newly installed plugin
    As a Shop Owner
    I want to easily generate invoices for orders placed before installation of the plugin

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And the invoices are not generated
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000777"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    Scenario: Generating invoices for previously placed orders
        When I generate invoices for previously placed orders
        And I browse invoices
        Then I should see a single invoice for order "#00000777"
