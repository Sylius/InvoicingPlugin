<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Webmozart\Assert\Assert;

final class InvoiceEmailContext implements Context
{
    /** @var EmailCheckerInterface */
    private $emailChecker;

    public function __construct(EmailCheckerInterface $emailChecker)
    {
        $this->emailChecker = $emailChecker;
    }

    /**
     * @Then an email containing invoice generated for order :orderNumber should be sent to :recipient
     */
    public function emailContainingInvoiceForOrderShouldBeSent(string $orderNumber, string $recipient): void
    {
        Assert::true($this->emailChecker->hasMessageTo(sprintf('was generated for order with number #%s', $orderNumber), $recipient));
    }

    /**
     * @Then an email containing invoice generated for order :orderNumber should not be sent to :recipient
     */
    public function emailContainingInvoiceForOrderShouldNotBeSent(string $orderNumber, string $recipient): void
    {
        Assert::false($this->emailChecker->hasMessageTo(
            sprintf('was generated for order with number %s', $orderNumber),
            $recipient
        ));
    }
}
