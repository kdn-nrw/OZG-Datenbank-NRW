<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211118154854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Onboarding data_completeness_confirmed property';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding ADD data_completeness_confirmed TINYINT(1) DEFAULT NULL');

        if (!$schema->getTable('ozg_onboarding_base_info')->hasColumn('ip_address')) {
            $this->addSql('ALTER TABLE ozg_onboarding_base_info ADD ip_address VARCHAR(255) DEFAULT NULL');
        }
        if (!$schema->getTable('ozg_meta_item_property')->hasColumn('placeholder')) {
            $this->addSql('ALTER TABLE ozg_meta_item_property ADD placeholder VARCHAR(255) DEFAULT NULL, ADD use_for_completeness_calculation TINYINT(1) DEFAULT NULL, ADD required TINYINT(1) NOT NULL');
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding DROP data_completeness_confirmed');
    }
}
