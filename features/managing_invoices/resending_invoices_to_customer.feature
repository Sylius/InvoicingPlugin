@managing_invoices @ui
Feature: Resending an invoice to a Customer
    In order to be able to resend an invoice to a Customer
    As an Administrator
    I want to have an appropriate button next to every invoice on Order view

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000777"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    Scenario: Being able to resend an invoice to Customer
        When I view the summary of the order "#00000777"
        And I resend the first invoice
        Then an email containing invoice generated for order "#00000777" should be sent to "lucy@teamlucifer.com"
        And I should be notified that the email was sent successfully
