@invoice_number_sequence @global_sequence_scope @pdf_enabled
Feature: Preventing duplicated invoice numbers
    In order to never expose an invoice of one customer to another
    As a Shop Owner
    I want invoice generation to fail loudly when it would reuse an already issued invoice number

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Pascaline Calculator Replica" priced at "$60.00"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And it is "2025-10-15" now
        And there is a customer "blaise.pascal@gmail.com" that placed an order "#00000001"
        And the customer bought a single "Pascaline Calculator Replica"
        And the customer "Blaise Pascal" addressed it to "Royal St", "70116" "New Orleans" in the "United States"
        And for the billing address of "Gilberte Périer" in the "Chartres St", "70116" "New Orleans", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And there is another customer "pierre.fermat@gmail.com" that placed an order "#00000002"
        And the customer bought a single "Pascaline Calculator Replica"
        And the customer "Pierre de Fermat" addressed it to "Toulouse St", "70116" "New Orleans" in the "United States"
        And for the billing address of "Pierre de Fermat" in the "Toulouse St", "70116" "New Orleans", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment

    @application
    Scenario: Failing loudly instead of reusing an already issued invoice number
        Given the order "#00000002" has lost all of its invoices
        And the invoice number sequences have been reset
        Then it should not be possible to generate an invoice for order "#00000002"
        And the order "#00000002" should have no invoice
        And the invoice for order "#00000001" should have number "2025/10/000000001"
        And the invoice for order "#00000001" should be saved on the server
