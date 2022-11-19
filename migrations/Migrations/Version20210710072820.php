<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210710072820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_implementation_project')->hasColumn('piloting_status_at')) {
            $this->addSql('ALTER TABLE ozg_implementation_project ADD piloting_status_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
            $this->addSql('UPDATE ozg_implementation_project SET piloting_status_at = commissioning_status_at WHERE commissioning_status_at IS NOT NULL');
            $this->addSql('UPDATE ozg_implementation_project SET commissioning_status_at = NULL WHERE commissioning_status_at IS NOT NULL');
            $this->addSql('INSERT INTO ozg_implementation_status (level, description, modified_at, created_at, name, hidden, next_status_id, set_automatically, prev_status_id, status_switch, color, css_class) VALUES (6, \'Pilotierung NRW\', NOW(), NOW(), \'Pilotierung NRW\', 0, 6, 1, 4, null, \'#04b701\', null)');
            $this->addSql('UPDATE ozg_implementation_status SET prev_status_id = 9 WHERE id = 6');
            $this->addSql('UPDATE ozg_implementation_status SET level = level + 1 WHERE level > 3');
            $this->addSql('UPDATE ozg_implementation_status SET level = 4 WHERE id = 9');
        }
        $this->addSql('DROP TABLE IF EXISTS ozg_confidence_level');
        $this->addSql('CREATE TABLE ozg_confidence_level (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, color VARCHAR(8) DEFAULT NULL, css_class VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_solution ADD confidence_level_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_solution ADD CONSTRAINT FK_595F587D417553B8 FOREIGN KEY (confidence_level_id) REFERENCES ozg_confidence_level (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_595F587D417553B8 ON ozg_solution (confidence_level_id)');
        $this->addSql('INSERT INTO ozg_confidence_level (name, hidden, color) VALUES (\'ohne Authentifizierung\', 0, \'#ffab91\')');
        $this->addSql('INSERT INTO ozg_confidence_level (name, hidden, color) VALUES (\'unterschwellig\', 0, \'#ffcc80\')');
        $this->addSql('INSERT INTO ozg_confidence_level (name, hidden, color) VALUES (\'normal\', 0, \'#ffe082\')');
        $this->addSql('INSERT INTO ozg_confidence_level (name, hidden, color) VALUES (\'substantiell\', 0, \'#78e894\')');
        $this->addSql('INSERT INTO ozg_confidence_level (name, hidden, color) VALUES (\'hoch\', 0, \'#54a300\')');
        $this->addSql('ALTER TABLE ozg_commune ADD transparency_portal_url VARCHAR(2048) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_implementation_project DROP piloting_status_at');
        $this->addSql('ALTER TABLE ozg_solution DROP FOREIGN KEY FK_595F587D417553B8');
        $this->addSql('DROP TABLE ozg_confidence_level');
        $this->addSql('DROP INDEX IDX_595F587D417553B8 ON ozg_solution');
        $this->addSql('ALTER TABLE ozg_solution DROP confidence_level_id');
        $this->addSql('ALTER TABLE ozg_commune DROP transparency_portal_url');
    }
}
