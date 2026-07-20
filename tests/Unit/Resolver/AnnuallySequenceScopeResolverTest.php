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

namespace Tests\Sylius\InvoicingPlugin\Unit\Resolver;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;
use Sylius\InvoicingPlugin\Resolver\AnnuallySequenceScopeResolver;

final class AnnuallySequenceScopeResolverTest extends TestCase
{
    #[Test]
    public function it_supports_only_the_annually_scope(): void
    {
        $resolver = new AnnuallySequenceScopeResolver();

        self::assertTrue($resolver->supports(InvoiceSequenceInterface::SCOPE_ANNUALLY));
        self::assertFalse($resolver->supports(InvoiceSequenceInterface::SCOPE_GLOBAL));
        self::assertFalse($resolver->supports(InvoiceSequenceInterface::SCOPE_MONTHLY));
    }

    #[Test]
    public function it_resolves_criteria_with_the_current_year_only(): void
    {
        $resolver = new AnnuallySequenceScopeResolver();

        self::assertSame(
            ['year' => 2025, 'month' => 0],
            $resolver->resolve(new \DateTimeImmutable('2025-10-15')),
        );
    }

    #[Test]
    public function it_prefixes_numbers_with_the_issue_year_and_month(): void
    {
        $resolver = new AnnuallySequenceScopeResolver();

        self::assertSame('2025/10/', $resolver->prefix(new \DateTimeImmutable('2025-10-15')));
    }
}
