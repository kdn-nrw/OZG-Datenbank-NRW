<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220321104446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding DROP FOREIGN KEY FK_FCF827A7C6C98E06');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A7C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD xta_server_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_contact ADD CONSTRAINT FK_E6BDC8EAEFC735C4 FOREIGN KEY (xta_server_id) REFERENCES ozg_onboarding_xta_server (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E6BDC8EAEFC735C4 ON ozg_onboarding_contact (xta_server_id)');
        if ($schema->getTable('ozg_onboarding_xta_server')->hasColumn('contact_name')) {
            $this->addSql('ALTER TABLE ozg_onboarding_xta_server DROP contact_name, DROP email, DROP phone_number, DROP mobile_number');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding DROP FOREIGN KEY FK_FCF827A7C6C98E06');
        $this->addSql('ALTER TABLE ozg_onboarding ADD CONSTRAINT FK_FCF827A7C6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP FOREIGN KEY FK_E6BDC8EAEFC735C4');
        $this->addSql('DROP INDEX UNIQ_E6BDC8EAEFC735C4 ON ozg_onboarding_contact');
        $this->addSql('ALTER TABLE ozg_onboarding_contact DROP xta_server_id');
        $this->addSql('ALTER TABLE ozg_onboarding_xta_server ADD contact_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD phone_number VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD mobile_number VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
