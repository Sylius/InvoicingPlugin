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

namespace Sylius\InvoicingPlugin\Generator;

use Symfony\Component\Config\FileLocatorInterface;

final class PdfOptionsGenerator implements PdfOptionsGeneratorInterface
{
    public function __construct(
        private readonly FileLocatorInterface $fileLocator,
        private readonly array $knpSnappyOptions,
        private readonly array $allowedFiles,
    ) {
    }

    public function generate(): array
    {
        $options = $this->knpSnappyOptions;

        if (empty($this->allowedFiles)) {
            return $options;
        }

        if (!isset($options['allow'])) {
            $options['allow'] = [];
        } elseif (!is_array($options['allow'])) {
            $options['allow'] = [$options['allow']];
        }

        $options['allow'] = array_merge(
            $options['allow'],
            array_map(fn ($file) => $this->fileLocator->locate($file), $this->allowedFiles),
        );

        return $options;
    }
}
