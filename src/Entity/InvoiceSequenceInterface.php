<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\VersionedInterface;

interface InvoiceSequenceInterface extends ResourceInterface, VersionedInterface
{
    public function getIndex(): int;

    public function incrementIndex(): void;
}
