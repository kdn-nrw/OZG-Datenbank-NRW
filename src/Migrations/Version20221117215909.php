<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221117215909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add model region project status';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_model_region_project ADD status INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_model_region_project_audit ADD status INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_model_region_project DROP status');
        $this->addSql('ALTER TABLE ozg_model_region_project_audit DROP status');
    }
}
