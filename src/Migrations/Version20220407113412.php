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
        $this->addSql('DROP TABLE ozg_contact_manufacturer');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info ADD allow_admin_access TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info_audit ADD allow_admin_access TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_contact_manufacturer (contact_id INT NOT NULL, manufacturer_id INT NOT NULL, INDEX IDX_B9DCD61FE7A1254A (contact_id), INDEX IDX_B9DCD61FA23B42D (manufacturer_id), PRIMARY KEY(contact_id, manufacturer_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ozg_contact_manufacturer ADD CONSTRAINT FK_B9DCD61FA23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_contact_manufacturer ADD CONSTRAINT FK_B9DCD61FE7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info DROP allow_admin_access');
        $this->addSql('ALTER TABLE ozg_onboarding_base_info_audit DROP allow_admin_access');
    }
}
