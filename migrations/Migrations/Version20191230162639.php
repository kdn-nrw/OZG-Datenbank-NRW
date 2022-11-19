<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191230162639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_laboratory_service (laboratory_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_C965562E2F2A371E (laboratory_id), INDEX IDX_C965562EED5CA9E6 (service_id), PRIMARY KEY(laboratory_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_laboratory_service ADD CONSTRAINT FK_C965562E2F2A371E FOREIGN KEY (laboratory_id) REFERENCES ozg_laboratory (id)');
        $this->addSql('ALTER TABLE ozg_laboratory_service ADD CONSTRAINT FK_C965562EED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_laboratory_service');
    }
}
