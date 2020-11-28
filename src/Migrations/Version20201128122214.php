<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201128122214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_api_service_base_result ADD commune_id INT DEFAULT NULL, ADD regional_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_api_service_base_result ADD CONSTRAINT FK_A0EA59A6131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A0EA59A6131A4F72 ON ozg_api_service_base_result (commune_id)');
        $this->addSql('ALTER TABLE ozg_commune ADD regional_key VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_api_service_base_result ADD service_created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_api_service_base_result DROP FOREIGN KEY FK_A0EA59A6131A4F72');
        $this->addSql('DROP INDEX IDX_A0EA59A6131A4F72 ON ozg_api_service_base_result');
        $this->addSql('ALTER TABLE ozg_api_service_base_result DROP commune_id, DROP regional_key');
        $this->addSql('ALTER TABLE ozg_commune DROP regional_key');
        $this->addSql('ALTER TABLE ozg_api_service_base_result DROP service_created_at');
    }
}
