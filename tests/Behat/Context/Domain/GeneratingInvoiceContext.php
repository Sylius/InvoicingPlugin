<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Tests\Application\InvoicingPlugin\EventProducer\TestOrderPlacedProducer;

final class GeneratingInvoiceContext implements Context
{
    /** @var TestOrderPlacedProducer */
    private $orderPlacedEventProducer;

    public function __construct(TestOrderPlacedProducer $orderPlacedEventProducer)
    {
        $this->orderPlacedEventProducer = $orderPlacedEventProducer;
    }

    /**
     * @Given the invoices are not generated
     */
    public function invoicesAreNotGenerated(): void
    {
        $this->orderPlacedEventProducer->disableInvoiceGeneration();
    }
}
