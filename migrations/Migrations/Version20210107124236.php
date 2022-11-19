<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210107124236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_implementation_project_service ADD id INT AUTO_INCREMENT NOT NULL, ADD status_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD hidden TINYINT(1) NOT NULL, CHANGE implementation_project_id implementation_project_id INT DEFAULT NULL, CHANGE service_id service_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_service ADD CONSTRAINT FK_11558B136BF700BD FOREIGN KEY (status_id) REFERENCES ozg_implementation_status (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_11558B136BF700BD ON ozg_implementation_project_service (status_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_implementation_project_service MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_project_service DROP FOREIGN KEY FK_11558B136BF700BD');
        $this->addSql('DROP INDEX IDX_11558B136BF700BD ON ozg_implementation_project_service');
        $this->addSql('ALTER TABLE ozg_implementation_project_service DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ozg_implementation_project_service DROP id, DROP status_id, DROP description, DROP modified_at, DROP created_at, DROP hidden, CHANGE service_id service_id INT NOT NULL, CHANGE implementation_project_id implementation_project_id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_project_service ADD PRIMARY KEY (implementation_project_id, service_id)');
    }
}
