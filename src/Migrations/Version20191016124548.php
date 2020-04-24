<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191016124548 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, representative VARCHAR(255) DEFAULT NULL, id_invoice VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_embeddable_backup(id INT AUTO_INCREMENT NOT NULL, id_invoice VARCHAR(255) NOT NULL, channel_code VARCHAR(255) NOT NULL,  shop_billing_data_company VARCHAR(255) DEFAULT NULL, shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL, shop_billing_data_street VARCHAR(255) DEFAULT NULL, shop_billing_data_city VARCHAR(255) DEFAULT NULL, shop_billing_data_postcode VARCHAR(255) DEFAULT NULL, shop_billing_data_country_code VARCHAR(255) DEFAULT NULL, shop_billing_data_representative VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('
            INSERT INTO sylius_invoicing_plugin_embeddable_backup (id_invoice, channel_code, shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative)
            SELECT id, channel_code, shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative
            FROM sylius_invoicing_plugin_invoice
        ');

        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD shop_billing_data_id INT DEFAULT NULL, ADD channel_id INT DEFAULT NULL, DROP channel_code, DROP channel_name, DROP shop_billing_data_company, DROP shop_billing_data_tax_id, DROP shop_billing_data_street, DROP shop_billing_data_city, DROP shop_billing_data_postcode, DROP shop_billing_data_country_code, DROP shop_billing_data_representative');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFCFE4AA36 FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice (shop_billing_data_id)');
        $this->addSql('CREATE INDEX IDX_3AA279BF72F5A1AA ON sylius_invoicing_plugin_invoice (channel_id)');

        $this->addSql('
            UPDATE sylius_invoicing_plugin_invoice
            INNER JOIN sylius_channel
            INNER JOIN sylius_invoicing_plugin_embeddable_backup
            SET sylius_invoicing_plugin_invoice.channel_id = sylius_channel.id
            WHERE sylius_channel.code = sylius_invoicing_plugin_embeddable_backup.channel_code
            AND sylius_invoicing_plugin_invoice.id = sylius_invoicing_plugin_embeddable_backup.id_invoice
        ');
        $this->addSql('
            INSERT INTO sylius_invoicing_plugin_shop_billing_data (company, tax_id, street, city, postcode, country_code, representative, id_invoice)
            SELECT shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative, id_invoice
            FROM sylius_invoicing_plugin_embeddable_backup
        ');
        $this->addSql('
            UPDATE sylius_invoicing_plugin_invoice
            INNER JOIN sylius_invoicing_plugin_shop_billing_data
            SET sylius_invoicing_plugin_invoice.shop_billing_data_id = sylius_invoicing_plugin_shop_billing_data.id
            WHERE sylius_invoicing_plugin_invoice.id = sylius_invoicing_plugin_shop_billing_data.id_invoice
        ');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_shop_billing_data DROP COLUMN id_invoice');

        $this->addSql('DROP TABLE sylius_invoicing_plugin_embeddable_backup');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFCFE4AA36');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF72F5A1AA');
        $this->addSql('DROP INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice');
        $this->addSql('DROP INDEX IDX_3AA279BF72F5A1AA ON sylius_invoicing_plugin_invoice');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD channel_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD channel_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_postcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_country_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_representative VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP shop_billing_data_id, DROP channel_id');
    }
}
