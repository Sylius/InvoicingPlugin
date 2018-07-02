<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('shopName', TextType::class, [
                'label' => 'sylius_invoicing_plugin.ui.shop_name',
                'required' => false,
            ])
            ->add('taxId', TextType::class, [
                'label' => 'sylius_invoicing_plugin.ui.tax_id',
                'required' => false,
            ])
            ->add('billingAddress', AddressType::class, [
                'label' => 'sylius_invoicing_plugin.ui.billing_address',
                'required' => false,
            ])
        ;
    }

    public function getExtendedType(): string
    {
        return ChannelType::class;
    }
}
