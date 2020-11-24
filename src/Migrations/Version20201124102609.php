<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201124102609 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE billing_data_id billing_data_id INT NOT NULL, CHANGE shop_billing_data_id shop_billing_data_id INT NOT NULL, CHANGE channel_id channel_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE billing_data_id billing_data_id INT DEFAULT NULL, CHANGE shop_billing_data_id shop_billing_data_id INT DEFAULT NULL, CHANGE channel_id channel_id INT DEFAULT NULL');
    }
}
