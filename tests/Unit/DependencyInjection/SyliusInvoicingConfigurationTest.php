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

namespace Tests\Sylius\InvoicingPlugin\Unit\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\DependencyInjection\Configuration;

final class SyliusInvoicingConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_does_not_define_any_allowed_files_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['pdf_generator' => ['allowed_files' => []]],
            'pdf_generator'
        );
    }

    /** @test */
    public function it_allows_to_define_allowed_files(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']]]],
            ['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']]],
            'pdf_generator'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
