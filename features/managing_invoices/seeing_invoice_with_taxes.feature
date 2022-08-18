@managing_invoices
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

    @ui
    Scenario: Seeing proper taxes on an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should have 2 "PHP T-Shirt" items with unit net price "60.00", discounted unit net price "60.00", net value "120.00", tax total "27.60" and total "147.60" in "USD" currency
        And it should have 3 "Symfony Mug" items with unit net price "40.00", discounted unit net price "40.00", net value "120.00", tax total "12.00" and total "132.00" in "USD" currency
        And it should have a tax item "10%" with amount "12.00" in "USD" currency
        And it should have a tax item "23%" with amount "27.60" in "USD" currency
        And its net total should be "250.00" in "USD" currency
        And its total should be "289.60" in "USD" currency
