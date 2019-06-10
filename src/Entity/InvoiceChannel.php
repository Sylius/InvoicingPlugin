<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/** @final */
class InvoiceChannel implements InvoiceChannelInterface
{
    /** @var string */
    protected $code;

    /** @var string */
    protected $name;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
