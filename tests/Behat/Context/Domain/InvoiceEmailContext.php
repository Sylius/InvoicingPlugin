<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Application\Kernel as SyliusKernel;
use Sylius\Behat\Service\Checker\EmailCheckerInterface;
use Webmozart\Assert\Assert;

if (SyliusKernel::MINOR_VERSION <= 11) {
    class_alias('Sylius\Component\Core\Test\Services\EmailCheckerInterface', 'Sylius\Behat\Service\Checker\EmailCheckerInterface');
}

final class InvoiceEmailContext implements Context
{
    private EmailCheckerInterface $emailChecker;

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
