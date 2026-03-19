@customer_browsing_invoices
Feature: Showing plain invoice numbers on orders list when PDF is disabled
    In order not to expose download links when PDF generation is disabled
    As a Customer
    I should see invoice numbers without links on the orders list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui @pdf_disabled
    Scenario: Invoice numbers are not links on orders list
        When I browse my orders
        Then I should not be able to download an invoice from my orders list
