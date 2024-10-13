@managing_invoices
Feature: Downloading an invoice from its details page
    In order to store a specific invoice
    As an Administrator
    I want to be able to download an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator
        And I set shop billing data for channel "United States" as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment

    @ui @pdf_enabled
    Scenario: Downloading an invoice
        Given I view the summary of the invoice for order "#00000666"
        When I download the invoice
        Then the pdf file for this invoice should be downloaded successfully
