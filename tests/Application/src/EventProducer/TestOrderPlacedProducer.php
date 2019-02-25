<?php

declare(strict_types=1);

namespace Tests\Application\InvoicingPlugin\EventProducer;

final class OrderPlacedProducer
{
    /** @var \Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer */
    private $decoratedOrderPlacedProducer;

    /** @var bool */
    private $shouldInvoiceBeGenerated;

    public function __construct(\Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer $decoratedOrderPlacedProducer)
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
        if ($this->shouldInvoiceBeGenerated) {
            return;
        }

        $this->decoratedOrderPlacedProducer->postUpdate($event);
    }

    public function setShouldInvoiceBeGenerated(bool $shouldInvoiceBeGenerated): void
    {
        $this->shouldInvoiceBeGenerated = $shouldInvoiceBeGenerated;
    }
}
