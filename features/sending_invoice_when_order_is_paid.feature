@managing_invoices
Feature: Sending invoice when order is paid
    In order to have a confirmation of order's payment
    As a Customer
    I want to receive an invoice via email when my order is paid

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
        And I placed an order "#00000667"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Receiving an email containing invoice when the order is paid
        When the order "#00000666" has just been paid
        Then an email containing invoice generated for order "#00000666" should be sent to "sylius@example.com"

    @ui
    Scenario: Not receiving an email containing invoice when the order is not paid
        When the order "#00000667" was cancelled
        Then an email containing invoice generated for order "#00000667" should not be sent to "sylius@example.com"
