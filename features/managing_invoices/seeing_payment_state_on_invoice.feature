@managing_invoices
Feature: Seeing payment state of an invoice
    In order to see if customer paid order
    As an Administrator
    I want to be able to view payment state of invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And the store allows paying with "Quick Payments"
        And I am logged in as an administrator
        And I set shop billing data for channel "United States" as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        Given the customer chose "UPS" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Seeing unpaid invoice details
        When I view the summary of the invoice for order "#00000666"
        And it should be unpaid

    @ui
    Scenario: Seeing invoice details after payment made
        Given this order is already paid
        When I view the summary of the invoice for order "#00000666"
        And it should be unpaid
