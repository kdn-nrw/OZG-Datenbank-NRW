<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200129134209 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_implementation_project_laboratory (implementation_project_id INT NOT NULL, laboratory_id INT NOT NULL, INDEX IDX_ABCC8CBD2CC68C60 (implementation_project_id), INDEX IDX_ABCC8CBD2F2A371E (laboratory_id), PRIMARY KEY(implementation_project_id, laboratory_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_laboratory ADD CONSTRAINT FK_ABCC8CBD2CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_laboratory ADD CONSTRAINT FK_ABCC8CBD2F2A371E FOREIGN KEY (laboratory_id) REFERENCES ozg_laboratory (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_implementation_project_laboratory');
    }
}
