<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200315110029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_service_specialized_procedures (service_id INT NOT NULL, specialized_procedure_id INT NOT NULL, INDEX IDX_3E394A3CED5CA9E6 (service_id), INDEX IDX_3E394A3C452D2882 (specialized_procedure_id), PRIMARY KEY(service_id, specialized_procedure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_service_specialized_procedures ADD CONSTRAINT FK_3E394A3CED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_specialized_procedures ADD CONSTRAINT FK_3E394A3C452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_service_specialized_procedures');
    }
}
