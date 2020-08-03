<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200801182834 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_efile ADD import_id INT DEFAULT NULL, ADD import_source VARCHAR(100) DEFAULT NULL');

        $this->addSql('CREATE TABLE ozg_implementation_project_fim_export (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_63BB938C2CC68C60 (implementation_project_id), INDEX IDX_63BB938CE7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export ADD CONSTRAINT FK_63BB938C2CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export ADD CONSTRAINT FK_63BB938CE7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export RENAME INDEX idx_63bb938c2cc68c60 TO IDX_F48525A82CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export RENAME INDEX idx_63bb938ce7a1254a TO IDX_F48525A8E7A1254A');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_efile DROP import_id, DROP import_source');
        $this->addSql('DROP TABLE ozg_implementation_project_fim_export');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export RENAME INDEX idx_f48525a82cc68c60 TO IDX_63BB938C2CC68C60');
        $this->addSql('ALTER TABLE ozg_implementation_project_fim_export RENAME INDEX idx_f48525a8e7a1254a TO IDX_63BB938CE7A1254A');
    }
}
