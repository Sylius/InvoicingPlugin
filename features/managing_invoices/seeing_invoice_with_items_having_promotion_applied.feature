@managing_invoices
Feature: Seeing an invoice with items and shipment having promotion applied
    In order to have items with proper amounts
    As an Administrator
    I want to be able to see items with properly distributed discounts on an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$10.00" discount to every order with items total at least "$15.00"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000666"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer bought 3 "Angel T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000666" is already paid

    @ui
    Scenario: Seeing proper amounts of line items having promotions applied on an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should have 2 "Angel T-Shirt" items with unit net price "10.00", discounted unit net price "6.67", net value "13.34", tax total "0.00" and total "13.34" in "USD" currency
        And it should have 1 "Angel T-Shirt" item with unit net price "10.00", discounted unit net price "6.66", net value "6.66", tax total "0.00" and total "6.66" in "USD" currency
        And it should have 1 "Galaxy Post" shipment with unit net price "20.00", discounted unit net price "20.00", net value "20.00", tax total "0.00" and total "20.00" in "USD" currency
        And its net total should be "40.00" in "USD" currency
        And its tax total should be "0.00" in "USD" currency
        And its total should be "40.00" in "USD" currency
