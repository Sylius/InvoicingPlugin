@managing_invoices
Feature: Seeing an invoice with items having promotion applied
    In order to have items with proper amounts
    As an Administrator
    I want to be able to see items with discounted prices on an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store has "Galaxy Post" shipping method with "$20.00" fee
        And the store allows paying with "Space money"
        And there is a promotion "50% shipping discount"
        And it gives "50%" discount on shipping to every order
        And there is a promotion "Anatomy Park Promotion"
        And this promotion gives "$1.00" off on every product with minimum price at "$5.00"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000666"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer chose "Galaxy Post" shipping method to "United States" with "Space money" payment
        And I am logged in as an administrator
        And the order "#00000666" is already paid

    @ui
    Scenario: Seeing proper amounts of line items having promotions applied on an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should have 2 "Angel T-Shirt" items with unit net price "10.00", discounted unit net price "9.00", net value "18.00", tax total "0.00" and total "18.00" in "USD" currency
        And it should have 1 "Galaxy Post" shipment with unit net price "20.00", discounted unit net price "10.00", net value "10.00", tax total "0.00" and total "10.00" in "USD" currency
        And its net total should be "28.00" in "USD" currency
        And its tax total should be "0.00" in "USD" currency
        And its total should be "28.00" in "USD" currency
