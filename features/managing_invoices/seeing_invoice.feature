@managing_invoices @ui
Feature: Seeing basic information about an invoice
    In order to see details of a specific invoice
    As an Administrator
    I want to be able to view basic information about an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "VAT" tax rate of 10% for "Clothes" within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And it belongs to "Clothes" tax category
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator
        And I set shop billing data for channel "United States" as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment

    Scenario: Seeing basic information about an invoice
        When I view the summary of the invoice for order "#00000666"
        Then it should be issued in the last hour
        And it should have billing data as "Mazikeen Lilim", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And it should have shop billing data as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And it should have 2 items in the list
        And it should have an item "Angel T-Shirt" with unit price "$60.00", quantity 2, tax total "$12.00" and total "$132.00"
        And it should have a shipping item "UPS" with unit price "$10.00", quantity 1, tax total "$0.00" and total "$10.00"
        And its total should be "$142.00"
        And it should be issued in "United States" channel

    Scenario: Downloading invoice
        Given I view the summary of the invoice for order "#00000666"
        When I download the invoice
        Then the pdf file for this invoice should be downloaded successfully

    Scenario: Going back to invoices view
        Given I view the summary of the invoice for order "#00000666"
        When I want to go back to invoices view
        Then I should see all invoices

    Scenario: Resending invoice
        Given I view the summary of the invoice for order "#00000666"
        When I resend invoice
        Then an email containing invoice generated for order "#00000777" should be sent to "lucy@teamlucifer.com"
