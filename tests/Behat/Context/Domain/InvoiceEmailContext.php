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
     * @Then an email containing invoice should be sent to :recipient
     */
    public function emailContainingInvoiceForOrderShouldBeSent(string $recipient): void
    {
        $this->emailChecker->hasRecipient($recipient);

        Assert::eq($this->emailChecker->countMessagesTo($recipient), 1);

        $this->emailChecker->hasMessageTo('Invoice generated', $recipient);
    }
}
