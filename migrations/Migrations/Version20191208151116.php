<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191208151116 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_laboratory_service_system (laboratory_id INT NOT NULL, service_system_id INT NOT NULL, INDEX IDX_2FBC06922F2A371E (laboratory_id), INDEX IDX_2FBC0692880415EF (service_system_id), PRIMARY KEY(laboratory_id, service_system_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_laboratory_service_system ADD CONSTRAINT FK_2FBC06922F2A371E FOREIGN KEY (laboratory_id) REFERENCES ozg_laboratory (id)');
        $this->addSql('ALTER TABLE ozg_laboratory_service_system ADD CONSTRAINT FK_2FBC0692880415EF FOREIGN KEY (service_system_id) REFERENCES ozg_service_system (id)');
        $this->addSql('ALTER TABLE ozg_laboratory ADD implementation_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_laboratory_service_system');
        $this->addSql('ALTER TABLE ozg_laboratory DROP implementation_url');
    }
}
