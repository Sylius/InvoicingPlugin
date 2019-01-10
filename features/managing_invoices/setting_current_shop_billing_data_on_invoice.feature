@managing_invoices @ui
Feature: Setting current shop billing data on an invoice
    In order to have proper shop billing data on invoices
    As an Administrator
    I want it to be immutable once set on a newly created invoice

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

    Scenario: Having immutable shop billing data on an invoice
        When I set shop billing data for channel "United States" as "Ankh Morpork", "2200220022", "Frost Alley", "90210" "Las Vegas", "United States"
        And I view the summary of the invoice for order "#00000666"
        Then it should still have shop billing data as "Ragnarok", "1100110011", "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
