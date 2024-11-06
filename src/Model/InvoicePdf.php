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

namespace Sylius\InvoicingPlugin\Model;

final class InvoicePdf
{
    private ?string $fullPath = null;

    public function __construct(
        private readonly string $filename,
        private readonly string $content,
    ) {
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function fullPath(): ?string
    {
        return $this->fullPath;
    }

    public function setFullPath(?string $fullPath): void
    {
        $this->fullPath = $fullPath;
    }
}
