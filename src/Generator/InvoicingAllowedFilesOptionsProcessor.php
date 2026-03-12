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

use Knp\Snappy\AbstractGenerator;
use Sylius\PdfGenerationBundle\Core\Processor\OptionsProcessorInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicingAllowedFilesOptionsProcessor implements OptionsProcessorInterface
{
    /** @param list<string> $allowedFiles */
    public function __construct(
        private readonly FileLocatorInterface $fileLocator,
        private readonly array $allowedFiles = [],
    ) {
    }

    /** @param AbstractGenerator $generator */
    public function process(object $generator, string $context = 'default'): void
    {
        $defaultAllowedFiles = $generator->getOptions()['allow'] ?? [];

        if ([] === $this->allowedFiles && [] === $defaultAllowedFiles) {
            return;
        }

        $generator->setOption(
            'allow',
            array_merge(
                $defaultAllowedFiles,
                array_map(
                    fn (string $file): string => $this->fileLocator->locate($file),
                    $this->allowedFiles,
                ),
            ),
        );
    }
}
