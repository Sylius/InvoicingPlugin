@managing_invoices @ui
Feature: Generating an invoice with a proper sequential number
    In order to have invoices generated
    As an Administrator
    I want to have invoices with proper sequential numbers

    Background:
        Given the store operates on a single channel in "United States"
        And the channel "United States" has shop billing data specified as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the store has a product "Angel T-Shirt" priced at "$60.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator

    Scenario: Generating an invoice with a proper sequential number
        Given the last invoice has been generated today as 4th in this month
        And there is a customer "lucy@teamlucifer.com" that placed an order "#000000666"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        When I view the summary of the invoice for order "#000000666"
        And it should have sequential number generated with "000000005" and current month and year

    Scenario: Generating an invoice with a proper sequential number
        Given the last invoice has been generated two months ago as 9th in that month
        And there is a customer "lucy@teamlucifer.com" that placed an order "#000000666"
        And the customer bought 2 "Angel T-Shirt" products
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        When I view the summary of the invoice for order "#000000666"
        Then it should be issued in the last hour
        And it should have sequential number generated with "000000001" and current month and year
