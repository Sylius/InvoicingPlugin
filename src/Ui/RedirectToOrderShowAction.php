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

namespace Sylius\InvoicingPlugin\Ui;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class RedirectToOrderShowAction
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var string $orderNumber */
        $orderNumber = $request->attributes->get('number');

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        return new RedirectResponse($this->router->generate(
            'sylius_admin_order_show',
            ['id' => $order->getId()],
        ));
    }
}
