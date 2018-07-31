<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/** @final */
class InvoiceSequence implements InvoiceSequenceInterface
{
    /** @var mixed */
    protected $id;

    /** @var int */
    protected $index = 0;

    /** @var \DateTimeInterface|null */
    protected $lastGeneratedAt;

    /** @var int|null */
    protected $version = 1;

    public function getId()
    {
        return $this->id;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function incrementIndex(): void
    {
        ++$this->index;
    }

    public function resetIndex(): void
    {
        $this->index = 0;
    }

    public function getLastGeneratedAt(): ?\DateTimeInterface
    {
        return $this->lastGeneratedAt;
    }

    public function setLastGeneratedAt(\DateTimeInterface $lastGeneratedAt): void
    {
        $this->lastGeneratedAt = $lastGeneratedAt;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }
}
