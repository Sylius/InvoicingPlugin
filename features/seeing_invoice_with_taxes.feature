@managing_invoices @ui
Feature: Seeing taxes on an invoice
    In order to have proper amount on an invoice
    As an Administrator
    I want to be able to view taxes on an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$40.00"
        And it belongs to "Mugs" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 2 "PHP T-Shirt" products
        And the customer bought 3 "Symfony Mug" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    Scenario: Seeing proper taxes on an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should have an item "PHP T-Shirt" with unit price "$60.00", quantity 2, tax total "$27.60" and total "$147.60"
        And it should have an item "Symfony Mug" with unit price "$40.00", quantity 3, tax total "$12.00" and total "$132.00"
        And its subtotal should be "$250.00"
        And it should have a tax item "US VAT (23%)" with amount "$27.60"
        And it should have a tax item "Low VAT (10%)" with amount "$12.00"
        And its total should be "$289.60"
