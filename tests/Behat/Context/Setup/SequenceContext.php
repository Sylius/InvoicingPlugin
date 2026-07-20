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

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class SequenceContext implements Context
{
    public function __construct(
        private readonly RepositoryInterface $sequenceRepository,
        private readonly ObjectManager $sequenceManager,
    ) {
    }

    /**
     * @Given the invoice number sequences have been reset
     */
    public function theInvoiceNumberSequencesHaveBeenReset(): void
    {
        foreach ($this->sequenceRepository->findAll() as $sequence) {
            $this->sequenceManager->remove($sequence);
        }

        $this->sequenceManager->flush();
    }
}
