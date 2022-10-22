<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221022152350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add indizes for ServiceBaseResults and FimTypes';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_REGIONAL_KEY ON ozg_api_service_base_result (regional_key)');
        $this->addSql('CREATE INDEX IDX_DATA_TYPE ON ozg_service_fim (data_type)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_REGIONAL_KEY ON ozg_api_service_base_result');
        $this->addSql('DROP INDEX IDX_DATA_TYPE ON ozg_service_fim');
    }
}
