@invoice_number_sequence @global_sequence_scope @pdf_enabled
Feature: Recovering lost invoice files
    In order to always hand invoices out to customers
    As a Shop Owner
    I want invoice files to be regenerated from the database when they are missing from the storage

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "ES-335 Semi-Hollow Guitar" priced at "$60.00"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And it is "2025-10-15" now
        And there is a customer "bb.king@gmail.com" that placed an order "#00000001"
        And the customer bought a single "ES-335 Semi-Hollow Guitar"
        And the customer "B.B. King" addressed it to "Beale St", "38103" "Memphis" in the "United States"
        And for the billing address of "Lucille King" in the "Union Ave", "38103" "Memphis", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment

    @application
    Scenario: Regenerating a lost invoice file on download
        Given the invoice file for order "#00000001" has been removed from the server
        Then the invoice for order "#00000001" should be downloadable with number "2025/10/000000001"
        And the invoice for order "#00000001" should be saved on the server
