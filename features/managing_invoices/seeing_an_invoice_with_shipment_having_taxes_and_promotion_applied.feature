@managing_invoices
Feature: Seeing an invoice with shipment having taxes and promotion applied
    In order to have the proper amount of shipment on an invoice
    As an Administrator
    I want to be able to view basic information about an invoice

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store has "VAT" tax rate of 23% for "Shipping Services" within the "US" zone
        And the store has "Space Pidgeons Post" shipping method with "$10.00" fee within the "US" zone
        And shipping method "Space Pidgeons Post" belongs to "Shipping Services" tax category
        And the store allows paying with "Space money"
        And there is a customer "rick.sanchez@wubba-lubba-dub-dub.com" that placed an order "#00000666"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer bought 2 "Angel T-Shirt" products
        And I am logged in as an administrator

    @ui
    Scenario: Seeing proper amount of shipment with taxes and a promotion applied
        And there is a promotion "50% shipping discount"
        And it gives "50%" discount on shipping to every order
        And the customer chose "Space Pidgeons Post" shipping method to "United States" with "Space money" payment
        And the order "#00000666" is already paid
        When I view the summary of the invoice for order "#00000666"
        Then it should have 1 "Space Pidgeons Post" shipment with unit price "5.00", net value "5.00", tax total "1.15" and total "6.15" in "USD" currency
        And it should have a tax item "VAT (23%)" with amount "1.15" in "USD" currency
        And its net total should be "25.00" in "USD" currency
        And its tax total should be "1.15" in "USD" currency
        And its total should be "26.15" in "USD" currency

    @ui
    Scenario: Seeing proper amount of shipment with taxes applied
        And the customer chose "Space Pidgeons Post" shipping method to "United States" with "Space money" payment
        And the order "#00000666" is already paid
        When I view the summary of the invoice for order "#00000666"
        Then it should have 1 "Space Pidgeons Post" shipment with unit price "10.00", net value "10.00", tax total "2.30" and total "12.30" in "USD" currency
        And it should have a tax item "VAT (23%)" with amount "2.30" in "USD" currency
        And its net total should be "30.00" in "USD" currency
        And its tax total should be "2.30" in "USD" currency
        And its total should be "32.30" in "USD" currency
