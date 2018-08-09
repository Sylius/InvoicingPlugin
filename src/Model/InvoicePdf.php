<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Model;

final class InvoicePdf
{
    /** @var string */
    private $filename;

    /** @var string */
    private $content;

    public function __construct(string $filename, string $content)
    {
        $this->filename = $filename;
        $this->content = $content;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function content(): string
    {
        return $this->content;
    }
}
