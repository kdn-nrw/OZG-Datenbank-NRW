<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200419152025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_commune_portals (commune_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_AB6770CB131A4F72 (commune_id), INDEX IDX_AB6770CBB887E1DD (portal_id), PRIMARY KEY(commune_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_commune_portals ADD CONSTRAINT FK_AB6770CB131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id)');
        $this->addSql('ALTER TABLE ozg_commune_portals ADD CONSTRAINT FK_AB6770CBB887E1DD FOREIGN KEY (portal_id) REFERENCES ozg_portal (id)');

        $this->addSql('CREATE TABLE ozg_implementation_project_service (implementation_project_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_11558B132CC68C60 (implementation_project_id), INDEX IDX_11558B13ED5CA9E6 (service_id), PRIMARY KEY(implementation_project_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_implementation_project_service ADD CONSTRAINT FK_11558B132CC68C60 FOREIGN KEY (implementation_project_id) REFERENCES ozg_implementation_project (id)');
        $this->addSql('ALTER TABLE ozg_implementation_project_service ADD CONSTRAINT FK_11558B13ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_commune_portals');
        $this->addSql('DROP TABLE ozg_implementation_project_service');
    }
}
