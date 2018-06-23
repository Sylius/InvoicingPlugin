Feature: Sending invoice when order is paid
    In order to have a confirmation of order's payment
    As a customer
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

    Scenario: I receive an email containing invoice once complete payment steps for order
        When I complete the payment step
        Then I should receive an email containing invoice for order "#00000666"
