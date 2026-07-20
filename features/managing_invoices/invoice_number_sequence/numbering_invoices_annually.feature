@invoice_number_sequence @annually_sequence_scope @pdf_enabled
Feature: Numbering invoices with an annual sequence
    In order to have invoice numbers restarting every year
    As a Shop Owner
    I want invoice numbers to be scoped to the year they are issued in

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Pascaline Calculator Replica" priced at "$60.00"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"

    @application
    Scenario: Continuing numbering across months of the same year and restarting in a new year
        Given it is "2025-11-20" now
        And there is a customer "blaise.pascal@gmail.com" that placed an order "#00000001"
        And the customer bought a single "Pascaline Calculator Replica"
        And the customer "Blaise Pascal" addressed it to "Royal St", "70116" "New Orleans" in the "United States"
        And for the billing address of "Gilberte Périer" in the "Chartres St", "70116" "New Orleans", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And it is "2025-12-31" now
        And there is another customer "pierre.fermat@gmail.com" that placed an order "#00000002"
        And the customer bought a single "Pascaline Calculator Replica"
        And the customer "Pierre de Fermat" addressed it to "Toulouse St", "70116" "New Orleans" in the "United States"
        And for the billing address of "Pierre de Fermat" in the "Toulouse St", "70116" "New Orleans", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And it is "2026-01-01" now
        And there is another customer "christiaan.huygens@gmail.com" that placed an order "#00000003"
        And the customer bought a single "Pascaline Calculator Replica"
        And the customer "Christiaan Huygens" addressed it to "Dauphine St", "70116" "New Orleans" in the "United States"
        And for the billing address of "Christiaan Huygens" in the "Dauphine St", "70116" "New Orleans", "United States"
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        Then the invoice for order "#00000001" should have number "2025/11/000000001"
        And the invoice for order "#00000002" should have number "2025/12/000000002"
        And the invoice for order "#00000003" should have number "2026/01/000000001"
