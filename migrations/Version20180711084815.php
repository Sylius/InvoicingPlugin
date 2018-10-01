<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711084815 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sylius_invoicing_plugin_sequence (id INTEGER NOT NULL, idx INTEGER NOT NULL, version INTEGER DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP number');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_sequence CHANGE id id INT NOT NULL');
        $this->addSql('DROP TABLE sylius_invoicing_plugin_sequence');
    }
}
