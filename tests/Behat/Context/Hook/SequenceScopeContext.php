<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Service\SwitchableSequenceScopeResolver;

final class SequenceScopeContext implements Context
{
    public function __construct(private readonly SwitchableSequenceScopeResolver $sequenceScopeResolver)
    {
    }

    /**
     * @BeforeScenario @monthly_sequence_scope
     */
    public function enableMonthlySequenceScope(): void
    {
        $this->sequenceScopeResolver->switchTo(InvoiceSequenceInterface::SCOPE_MONTHLY);
    }

    /**
     * @BeforeScenario @annually_sequence_scope
     */
    public function enableAnnuallySequenceScope(): void
    {
        $this->sequenceScopeResolver->switchTo(InvoiceSequenceInterface::SCOPE_ANNUALLY);
    }

    /**
     * @AfterScenario
     */
    public function resetSequenceScope(): void
    {
        $this->sequenceScopeResolver->switchTo(null);
    }
}
