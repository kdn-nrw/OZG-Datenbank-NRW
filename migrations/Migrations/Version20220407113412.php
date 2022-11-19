<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220407113412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_base_info ADD allow_admin_access TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info_audit ADD allow_admin_access TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_base_info DROP allow_admin_access');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info_audit DROP allow_admin_access');
    }
}
