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

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191016124548 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');

        if ($databasePlatform === 'mysql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, representative VARCHAR(255) DEFAULT NULL, id_invoice VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_embeddable_backup(id INT AUTO_INCREMENT NOT NULL, id_invoice VARCHAR(255) NOT NULL, channel_code VARCHAR(255) NOT NULL,  shop_billing_data_company VARCHAR(255) DEFAULT NULL, shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL, shop_billing_data_street VARCHAR(255) DEFAULT NULL, shop_billing_data_city VARCHAR(255) DEFAULT NULL, shop_billing_data_postcode VARCHAR(255) DEFAULT NULL, shop_billing_data_country_code VARCHAR(255) DEFAULT NULL, shop_billing_data_representative VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data ( id SERIAL PRIMARY KEY, company VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, postcode VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, representative VARCHAR(255) DEFAULT NULL, id_invoice VARCHAR(255) DEFAULT NULL )');
            $this->addSql('CREATE TABLE sylius_invoicing_plugin_embeddable_backup ( id SERIAL PRIMARY KEY, id_invoice VARCHAR(255) NOT NULL, channel_code VARCHAR(255) NOT NULL, shop_billing_data_company VARCHAR(255) DEFAULT NULL, shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL, shop_billing_data_street VARCHAR(255) DEFAULT NULL, shop_billing_data_city VARCHAR(255) DEFAULT NULL, shop_billing_data_postcode VARCHAR(255) DEFAULT NULL, shop_billing_data_country_code VARCHAR(255) DEFAULT NULL, shop_billing_data_representative VARCHAR(255) DEFAULT NULL )');
        }

        $this->addSql('
            INSERT INTO sylius_invoicing_plugin_embeddable_backup (id_invoice, channel_code, shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative)
            SELECT id, channel_code, shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative
            FROM sylius_invoicing_plugin_invoice
        ');

        if ($databasePlatform === 'mysql') {
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
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD COLUMN shop_billing_data_id INT DEFAULT NULL, ADD COLUMN channel_id INT DEFAULT NULL, DROP COLUMN channel_code, DROP COLUMN channel_name, DROP COLUMN shop_billing_data_company, DROP COLUMN shop_billing_data_tax_id, DROP COLUMN shop_billing_data_street, DROP COLUMN shop_billing_data_city, DROP COLUMN shop_billing_data_postcode, DROP COLUMN shop_billing_data_country_code, DROP COLUMN shop_billing_data_representative');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BFCFE4AA36 FOREIGN KEY (shop_billing_data_id) REFERENCES sylius_invoicing_plugin_shop_billing_data (id)');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD CONSTRAINT FK_3AA279BF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice (shop_billing_data_id)');
            $this->addSql('CREATE INDEX IDX_3AA279BF72F5A1AA ON sylius_invoicing_plugin_invoice (channel_id)');

            $this->addSql('UPDATE sylius_invoicing_plugin_invoice SET channel_id = sylius_channel.id FROM sylius_channel JOIN sylius_invoicing_plugin_embeddable_backup ON sylius_channel.code = sylius_invoicing_plugin_embeddable_backup.channel_code WHERE sylius_invoicing_plugin_invoice.id = sylius_invoicing_plugin_embeddable_backup.id_invoice');
            $this->addSql('INSERT INTO sylius_invoicing_plugin_shop_billing_data ( company, tax_id, street, city, postcode, country_code, representative, id_invoice ) SELECT shop_billing_data_company, shop_billing_data_tax_id, shop_billing_data_street, shop_billing_data_city, shop_billing_data_postcode, shop_billing_data_country_code, shop_billing_data_representative, id_invoice FROM sylius_invoicing_plugin_embeddable_backup');
            $this->addSql('UPDATE sylius_invoicing_plugin_invoice SET shop_billing_data_id = sylius_invoicing_plugin_shop_billing_data.id FROM sylius_invoicing_plugin_shop_billing_data WHERE sylius_invoicing_plugin_invoice.id = sylius_invoicing_plugin_shop_billing_data.id_invoice');

            $this->addSql('ALTER TABLE sylius_invoicing_plugin_shop_billing_data DROP COLUMN id_invoice');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_embeddable_backup');
        }

    }

    public function down(Schema $schema): void
    {
        $databasePlatform = $this->connection->getDatabasePlatform()->getName();
        $this->abortIf($databasePlatform !== 'mysql' && $databasePlatform !== 'postgresql', 'Migration can only be executed safely on \'mysql\' or \'postgres\'.');


        if ($databasePlatform === 'mysql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BFCFE4AA36');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP FOREIGN KEY FK_3AA279BF72F5A1AA');
            $this->addSql('DROP INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice');
            $this->addSql('DROP INDEX IDX_3AA279BF72F5A1AA ON sylius_invoicing_plugin_invoice');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD channel_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD channel_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_company VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_street VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_city VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_postcode VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_country_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD shop_billing_data_representative VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP shop_billing_data_id, DROP channel_id');
        } elseif ($databasePlatform === 'postgresql') {
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP CONSTRAINT FK_3AA279BFCFE4AA36');
            $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP CONSTRAINT FK_3AA279BF72F5A1AA');
            $this->addSql('DROP INDEX UNIQ_3AA279BFCFE4AA36 ON sylius_invoicing_plugin_invoice');
            $this->addSql('DROP INDEX IDX_3AA279BF72F5A1AA ON sylius_invoicing_plugin_invoice');
            $this->addSql('CREATE COLLATION IF NOT EXISTS utf8_unicode_ci ( LOCALE = \'en_US.utf8\' )');
            $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD COLUMN channel_code VARCHAR(255) COLLATE "utf8_unicode_ci" NOT NULL, ADD COLUMN channel_name VARCHAR(255) COLLATE "utf8_unicode_ci" NOT NULL, ADD COLUMN shop_billing_data_company VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_tax_id VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_street VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_city VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_postcode VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_country_code VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, ADD COLUMN shop_billing_data_representative VARCHAR(255) COLLATE "utf8_unicode_ci" DEFAULT NULL, DROP COLUMN shop_billing_data_id, DROP COLUMN channel_id');
        }
    }
}
