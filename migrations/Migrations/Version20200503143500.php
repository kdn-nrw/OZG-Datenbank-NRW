<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200503143500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_services_portals (service_id INT NOT NULL, portal_id INT NOT NULL, INDEX IDX_701DBFBDED5CA9E6 (service_id), INDEX IDX_701DBFBDB887E1DD (portal_id), PRIMARY KEY(service_id, portal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_services_portals ADD CONSTRAINT FK_701DBFBDED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_services_portals ADD CONSTRAINT FK_701DBFBDB887E1DD FOREIGN KEY (portal_id) REFERENCES ozg_portal (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_services_portals');
    }
}
