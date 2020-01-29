<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200129141934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_bureau_service (bureau_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_69E20C1232516FE2 (bureau_id), INDEX IDX_69E20C12ED5CA9E6 (service_id), PRIMARY KEY(bureau_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_bureau_service ADD CONSTRAINT FK_69E20C1232516FE2 FOREIGN KEY (bureau_id) REFERENCES ozg_bureau (id)');
        $this->addSql('ALTER TABLE ozg_bureau_service ADD CONSTRAINT FK_69E20C12ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service ADD inherit_bureaus TINYINT(1) NOT NULL');
        $this->addSql('UPDATE ozg_service SET inherit_bureaus = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_bureau_service');
        $this->addSql('ALTER TABLE ozg_service DROP inherit_bureaus');
    }
}
