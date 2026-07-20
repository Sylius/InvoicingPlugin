@invoice_number_sequence @global_sequence_scope @pdf_enabled
Feature: Numbering invoices with a global sequence
    In order to have gapless, ever-increasing invoice numbers
    As a Shop Owner
    I want invoice numbers to keep incrementing regardless of the passing months and years

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "ES-335 Semi-Hollow Guitar" priced at "$60.00"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"

    @application
    Scenario: Continuing numbering across months and years
        Given it is "2025-12-20" now
        And there is a customer "bb.king@gmail.com" that placed an order "#00000001"
        And the customer bought a single "ES-335 Semi-Hollow Guitar"
        And the customer "B.B. King" addressed it to "Beale St", "38103" "Memphis" in the "United States"
        And for the billing address of "Lucille King" in the "Union Ave", "38103" "Memphis", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And it is "2026-01-05" now
        And there is another customer "chuck.berry@gmail.com" that placed an order "#00000002"
        And the customer bought a single "ES-335 Semi-Hollow Guitar"
        And the customer "Chuck Berry" addressed it to "Gibson Ave", "49001" "Kalamazoo" in the "United States"
        And for the billing address of "Chuck Berry" in the "Gibson Ave", "49001" "Kalamazoo", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        Then the invoice for order "#00000001" should have number "2025/12/000000001"
        And the invoice for order "#00000002" should have number "2026/01/000000002"
