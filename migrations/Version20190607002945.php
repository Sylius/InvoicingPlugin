<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190607002945 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sylius_invoicing_plugin_shop_billing_data');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE shopBillingData_company shop_billing_data_company VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_tax_id shop_billing_data_tax_id VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_street shop_billing_data_street VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_city shop_billing_data_city VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_postcode shop_billing_data_postcode VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_country_code shop_billing_data_country_code VARCHAR(255) DEFAULT NULL, CHANGE shopBillingData_representative shop_billing_data_representative VARCHAR(255) DEFAULT NULL, CHANGE channel_code channel_code VARCHAR(255) NOT NULL, CHANGE channel_name channel_name VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_shop_billing_data (id INT AUTO_INCREMENT NOT NULL, company VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, tax_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, street VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, city VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, postcode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, country_code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE shop_billing_data_company shopBillingData_company VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_tax_id shopBillingData_tax_id VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_street shopBillingData_street VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_city shopBillingData_city VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_postcode shopBillingData_postcode VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_country_code shopBillingData_country_code VARCHAR(255) DEFAULT NULL, CHANGE shop_billing_data_representative shopBillingData_representative VARCHAR(255) DEFAULT NULL, CHANGE channel_code channel_code VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE channel_name channel_name VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
