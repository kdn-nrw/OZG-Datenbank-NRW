<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319141645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the onboarding XTA server table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_xta_server (id INT NOT NULL, application_type INT DEFAULT NULL, intermediary_operator_type INT DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, authority_category VARCHAR(100) DEFAULT NULL, organizational_key VARCHAR(100) DEFAULT NULL, contact_name VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, osci_private_key_password VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(100) DEFAULT NULL, mobile_number VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_xta_server ADD CONSTRAINT FK_1A4E5570BF396750 FOREIGN KEY (id) REFERENCES ozg_onboarding (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_onboarding_xta_server');
    }
}
