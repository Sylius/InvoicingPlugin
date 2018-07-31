<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\VersionedInterface;

interface InvoiceSequenceInterface extends ResourceInterface, VersionedInterface
{
    public function getIndex(): int;

    public function incrementIndex(): void;

    public function resetIndex(): void;

    public function getLastGeneratedAt(): ?\DateTimeInterface;

    public function setLastGeneratedAt(\DateTimeInterface $lastGeneratedAt): void;
}
