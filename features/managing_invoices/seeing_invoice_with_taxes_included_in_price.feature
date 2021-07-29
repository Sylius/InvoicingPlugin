@managing_invoices @ui
Feature: Seeing included in price taxes on an invoice
    In order to have proper amount on an invoice
    As an Administrator
    I want to be able to view taxes on an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has included in price "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has included in price "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
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
        Then it should have 2 "PHP T-Shirt" items with unit price "48.78", net value "97.56", tax total "22.44" and total "120.00" in "USD" currency
        And it should have 2 "Symfony Mug" items with unit price "36.36", net value "72.72", tax total "7.28" and total "80.00" in "USD" currency
        And it should have 1 "Symfony Mug" items with unit price "36.37", net value "36.37", tax total "3.63" and total "40.00" in "USD" currency
        And it should have a tax item "23%" with amount "22.44" in "USD" currency
        And it should have a tax item "10%" with amount "10.91" in "USD" currency
        And its net total should be "216.65" in "USD" currency
        And its total should be "250.00" in "USD" currency
