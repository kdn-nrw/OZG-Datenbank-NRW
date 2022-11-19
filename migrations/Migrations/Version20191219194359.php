<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219194359 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_ministry_state_service_system (ministry_state_id INT NOT NULL, service_system_id INT NOT NULL, INDEX IDX_3324802E803929A9 (ministry_state_id), INDEX IDX_3324802E880415EF (service_system_id), PRIMARY KEY(ministry_state_id, service_system_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_ministry_state_service_system ADD CONSTRAINT FK_3324802E803929A9 FOREIGN KEY (ministry_state_id) REFERENCES ozg_ministry_state (id)');
        $this->addSql('ALTER TABLE ozg_ministry_state_service_system ADD CONSTRAINT FK_3324802E880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_ministry_state_service_system');
    }
}
