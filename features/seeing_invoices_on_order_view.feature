@managing_invoices
Feature: Seeing invoices on Customer's order view
    In order to be aware of all invoices related to the order placed by Customer
    As an Administrator
    I want to be see all related invoices on order view

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "PHP T-Shirt"
    And the store ships everywhere for free
    And the store allows paying with "Cash on Delivery"
    And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
    And the customer bought a single "PHP T-Shirt"
    And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
    And I am logged in as an administrator

    @ui
    Scenario: Seeing a new invoice related to the order
        When I view the summary of the order "#00000022"
        Then I should see an invoice related to this order

    @ui
    Scenario: Seeing invoice details when clicking its id on order view
        When I view the summary of the order "#00000022"
        And I click on first invoice's id
        Then I should see the summary of the invoice for order "#00000022"
