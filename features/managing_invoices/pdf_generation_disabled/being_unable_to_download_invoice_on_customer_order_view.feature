@customer_browsing_invoices
Feature: Being unable to download an invoice on a single order view
    In order not to generate a PDF file for an invoice related to the order
    As a Customer
    I want to be unable to download an invoice on a single order view

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
    Scenario: Being unable to download an invoice on a single order view
        When I view the summary of the order "#00000666"
        Then I should not be able to download the first invoice
