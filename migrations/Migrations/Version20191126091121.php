<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126091121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_service DROP FOREIGN KEY FK_81358EE8880415EF');
        $this->addSql('ALTER TABLE ozg_service ADD CONSTRAINT FK_81358EE8880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_service DROP FOREIGN KEY FK_81358EE8880415EF');
        $this->addSql('ALTER TABLE ozg_service ADD CONSTRAINT FK_81358EE8880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
    }
}
