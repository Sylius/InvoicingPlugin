<?php

namespace Tests\Application\InvoicingPlugin\AppBundle\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Sylius\Component\Core\Model\Channel as BaseChannel;
use Sylius\InvoicingPlugin\Entity\ShopBillingDataAwareInterface;
use Sylius\InvoicingPlugin\Entity\ShopBillingDataTrait;

/**
 * @Entity
 * @Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ShopBillingDataAwareInterface
{
    use ShopBillingDataTrait;
}
