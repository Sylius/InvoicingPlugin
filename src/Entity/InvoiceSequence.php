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

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\InvoicingPlugin\Enum\InvoiceSequenceScopeEnum;

/** @final */
class InvoiceSequence implements InvoiceSequenceInterface
{
    /** @var mixed */
    protected $id;

    protected int $index = 0;

    protected ?int $version = 1;

    protected InvoiceSequenceScopeEnum $type = InvoiceSequenceScopeEnum::GLOBAL;

    protected int $year = 0;

    protected int $month = 0;

    /** @return mixed */
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

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): void
    {
        $this->version = $version;
    }

    public function getType(): InvoiceSequenceScopeEnum
    {
        return $this->type;
    }

    public function setType(InvoiceSequenceScopeEnum $type): void
    {
        $this->type = $type;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): void
    {
        $this->month = $month;
    }
}
