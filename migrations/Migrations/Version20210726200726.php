<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210726200726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_service_account ADD client_id_2 VARCHAR(255) DEFAULT NULL, ADD client_password_2 VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE ozg_meta_item_property SET internal_label = \'Ansprechperson Servicekonto\' WHERE meta_key = \'group_general_admin_account\'');
        $this->addSql('UPDATE ozg_meta_item_property SET internal_label = \'Benachrichtigungen zum Servicekonto\' WHERE meta_key = \'group_general_mandator_email\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_service_account DROP client_id_2, DROP client_password_2');
    }
}
