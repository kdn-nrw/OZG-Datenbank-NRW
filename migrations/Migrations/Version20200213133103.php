<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200213133103 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_service_jurisdiction (service_id INT NOT NULL, jurisdiction_id INT NOT NULL, INDEX IDX_40FF826ED5CA9E6 (service_id), INDEX IDX_40FF8268C52AF17 (jurisdiction_id), PRIMARY KEY(service_id, jurisdiction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_specialized_procedure_service_provider (specialized_procedure_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_1CD89358452D2882 (specialized_procedure_id), INDEX IDX_1CD89358C6C98E06 (service_provider_id), PRIMARY KEY(specialized_procedure_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_specialized_procedure_commune (specialized_procedure_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_4FE0CADB452D2882 (specialized_procedure_id), INDEX IDX_4FE0CADB131A4F72 (commune_id), PRIMARY KEY(specialized_procedure_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_service_jurisdiction ADD CONSTRAINT FK_40FF826ED5CA9E6 FOREIGN KEY (service_id) REFERENCES ozg_service (id)');
        $this->addSql('ALTER TABLE ozg_service_jurisdiction ADD CONSTRAINT FK_40FF8268C52AF17 FOREIGN KEY (jurisdiction_id) REFERENCES ozg_jurisdiction (id)');
        $this->addSql('ALTER TABLE ozg_specialized_procedure_service_provider ADD CONSTRAINT FK_1CD89358452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_specialized_procedure_service_provider ADD CONSTRAINT FK_1CD89358C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_specialized_procedure_commune ADD CONSTRAINT FK_4FE0CADB452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_specialized_procedure_commune ADD CONSTRAINT FK_4FE0CADB131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_service ADD inherit_jurisdictions TINYINT(1) NOT NULL');
        $this->addSql('UPDATE ozg_service SET inherit_jurisdictions = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_service_jurisdiction');
        $this->addSql('DROP TABLE ozg_specialized_procedure_service_provider');
        $this->addSql('DROP TABLE ozg_specialized_procedure_commune');
        $this->addSql('ALTER TABLE ozg_service DROP inherit_jurisdictions');
    }
}
