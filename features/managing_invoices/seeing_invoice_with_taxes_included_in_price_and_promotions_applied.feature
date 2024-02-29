@managing_invoices @ui
Feature: Seeing included in price taxes and promotions applied on an invoice
    In order to have proper amount on an invoice
    As an Administrator
    I want to be able to view taxes and promotions on an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has included in price "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$10.00" off on every product with minimum price at "$5.00"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 2 "PHP T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    Scenario: Seeing proper taxes and promotions on an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should have 2 "PHP T-Shirt" items with unit net price "50.65", discounted unit net price "40.65", net value "81.30", tax total "18.70" and total "100.00" in "USD" currency
        And it should have a tax item "23%" with amount "18.70" in "USD" currency
        And its total should be "110.00" in "USD" currency
