@customer_browsing_invoices @ui
Feature: Downloading invoices on a single order view
    In order to have access to all invoices related to the order that I've placed
    As a Customer
    I want to be able to download invoices on a single order view

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

    Scenario: Downloading an invoice on order view
        When I view the summary of the order "#00000666"
        And I download the first invoice
        Then the pdf file for this invoice should be downloaded successfully
