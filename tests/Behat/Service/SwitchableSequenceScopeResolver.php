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

namespace Tests\Sylius\InvoicingPlugin\Behat\Service;

use Sylius\InvoicingPlugin\Resolver\SequenceScopeResolverInterface;

final class SwitchableSequenceScopeResolver implements SequenceScopeResolverInterface
{
    private ?string $scope = null;

    /** @param iterable<SequenceScopeResolverInterface> $decoratedResolvers */
    public function __construct(private readonly iterable $decoratedResolvers)
    {
    }

    public function switchTo(?string $scope): void
    {
        $this->scope = $scope;
    }

    public function supports(string $scope): bool
    {
        return null !== $this->scope;
    }

    public function resolve(\DateTimeImmutable $now): array
    {
        return $this->getResolver()->resolve($now);
    }

    public function prefix(\DateTimeImmutable $now): string
    {
        return $this->getResolver()->prefix($now);
    }

    private function getResolver(): SequenceScopeResolverInterface
    {
        foreach ($this->decoratedResolvers as $resolver) {
            if ($resolver->supports((string) $this->scope)) {
                return $resolver;
            }
        }

        throw new \RuntimeException(sprintf('No sequence scope resolver supports the "%s" scope', $this->scope));
    }
}
