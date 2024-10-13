@managing_invoices
Feature: Saving invoices on server during generation
    In order to keep invoices immutable for further usage
    As a Store Owner
    I want the invoices to be saved on a server during generation

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And channel "United States" has shop billing data set as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"

    @application @pdf_enabled
    Scenario: Having invoice saved on the server after the order is placed
        Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        When the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        Then the invoice for order "#00000666" should be saved on the server
