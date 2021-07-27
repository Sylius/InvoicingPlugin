<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PercentFromLabelExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('percent_from_label', [$this, 'getPercentFromLabel']),
        ];
    }

    public function getPercentFromLabel(string $label): string
    {
        $matches = [];

        preg_match("#\(.*?\)#", $label, $matches);

        return str_replace(['(', ')'], '', $matches[0]);
    }
}