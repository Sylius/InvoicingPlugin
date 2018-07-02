<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\InvoicingPlugin\Form\Type\ShopBillingDataType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('billingData', ShopBillingDataType::class, [
                'label' => 'sylius_invoicing_plugin.ui.billing_data',
                'required' => false,
            ])
        ;
    }

    public function getExtendedType(): string
    {
        return ChannelType::class;
    }
}
