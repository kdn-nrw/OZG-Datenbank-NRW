<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200531203257 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_ministry_state CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_office CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_organisation CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_commune CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_portal CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_solution CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_laboratory CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_manufacturer CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_form_server CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service_provider CHANGE url url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_payment_type CHANGE url url VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_commune CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_form_server CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_laboratory CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_manufacturer CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_ministry_state CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_office CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_organisation CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_payment_type CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_portal CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_service_provider CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_solution CHANGE url url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
