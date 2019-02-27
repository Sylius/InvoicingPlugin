<?php

declare(strict_types=1);

namespace Tests\Application\InvoicingPlugin\EventProducer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer;

final class TestOrderPlacedProducer
{
    /** @var OrderPlacedProducer */
    private $decoratedOrderPlacedProducer;

    /** @var bool */
    private $shouldInvoiceBeGenerated;

    public function __construct(OrderPlacedProducer $decoratedOrderPlacedProducer)
    {
        $this->decoratedOrderPlacedProducer = $decoratedOrderPlacedProducer;
        $this->shouldInvoiceBeGenerated = true;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        if (!$this->shouldInvoiceBeGenerated) {
            return;
        }

        $this->decoratedOrderPlacedProducer->postPersist($event);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        if (!$this->shouldInvoiceBeGenerated) {
            return;
        }

        $this->decoratedOrderPlacedProducer->postUpdate($event);
    }

    public function disableInvoiceGeneration(): void
    {
        $this->shouldInvoiceBeGenerated = false;
    }
}
