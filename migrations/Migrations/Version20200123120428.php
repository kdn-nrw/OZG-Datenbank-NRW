<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200123120428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_implementation_project_contact_interested (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_57D4B5932CC68C60 (implementation_project_id), INDEX IDX_57D4B593E7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_implementation_project_contact_participation (implementation_project_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_63F2EE702CC68C60 (implementation_project_id), INDEX IDX_63F2EE70E7A1254A (contact_id), PRIMARY KEY(implementation_project_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_interested ADD CONSTRAINT FK_57D4B5932CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_interested ADD CONSTRAINT FK_57D4B593E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_participation ADD CONSTRAINT FK_63F2EE702CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_contact_participation ADD CONSTRAINT FK_63F2EE70E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_implementation_project_contact_interested');
        $this->addSql('DROP TABLE ozg_implementation_project_contact_participation');
    }
}
