@managing_invoices
Feature: Being unable to download an invoice on a single order view
    In order not to generate PDF files for invoices related to the order
    As an Administrator
    I want to be unable to download invoices on a single order view

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @pdf_disabled
    Scenario: Being unable to download an invoice on a single order view
        When I view the summary of the order "#00000022"
        Then I should not be able to download the first invoice
