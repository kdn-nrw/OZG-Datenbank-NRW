<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220320195706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_document (id INT AUTO_INCREMENT NOT NULL, onboarding_id INT DEFAULT NULL, document_type VARCHAR(50) DEFAULT NULL, file_size INT DEFAULT NULL, local_name VARCHAR(255) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_B01EE6A0235CA921 (onboarding_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_document ADD CONSTRAINT FK_B01EE6A0235CA921 FOREIGN KEY (onboarding_id) REFERENCES ozg_onboarding (id)');
        $this->addSql('ALTER TABLE ozg_onboarding_xta_server DROP osci_public_key_name, DROP osci_public_key_original_name, DROP osci_public_key_size, DROP osci_private_key_name, DROP osci_private_key_original_name, DROP osci_private_key_size');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_onboarding_document');
        $this->addSql('ALTER TABLE ozg_onboarding_xta_server ADD osci_public_key_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD osci_public_key_original_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD osci_public_key_size INT DEFAULT NULL, ADD osci_private_key_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD osci_private_key_original_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD osci_private_key_size INT DEFAULT NULL');
    }
}
