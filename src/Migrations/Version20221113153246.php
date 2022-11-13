<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113153246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove commune solution ready fields; add commune portal interface url; add commune solution contacts';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_solutions_communes')->hasColumn('solution_ready')) {
            $this->addSql('ALTER TABLE ozg_solutions_communes DROP solution_ready, DROP solution_ready_at');
        }
        if (!$schema->hasTable('ozg_solutions_communes_contact')) {
            $this->addSql('CREATE TABLE ozg_solutions_communes_contact (solutions_communes_id INT NOT NULL, contact_id INT NOT NULL, INDEX IDX_8998A4073ED227F6 (solutions_communes_id), INDEX IDX_8998A407E7A1254A (contact_id), PRIMARY KEY(solutions_communes_id, contact_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_solutions_communes_contact ADD CONSTRAINT FK_8998A4073ED227F6 FOREIGN KEY (solutions_communes_id) REFERENCES ozg_solutions_communes (id)');
            $this->addSql('ALTER TABLE ozg_solutions_communes_contact ADD CONSTRAINT FK_8998A407E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        }
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_commune')->hasColumn('portal_interface_url')) {
            $this->addSql('ALTER TABLE ozg_commune ADD portal_interface_url VARCHAR(2048) DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_commune_audit ADD portal_interface_url VARCHAR(2048) DEFAULT NULL');
        }
        $this->addSql('UPDATE ozg_implementation_project SET status_id = 6 WHERE status_id = 8');
        $this->addSql('UPDATE ozg_implementation_status SET next_status_id = NULL WHERE id = 6');
        $this->addSql('DELETE FROM ozg_implementation_status WHERE id = 8');
        if ($schema->getTable('ozg_commune')->hasColumn('contact')) {
            $this->addSql('ALTER TABLE ozg_commune DROP contact');
            $this->addSql('ALTER TABLE ozg_commune_audit DROP contact');
        }
        if ($schema->getTable('ozg_solution')->hasColumn('contact')) {
            $this->addSql('ALTER TABLE ozg_solution DROP contact');
            $this->addSql('ALTER TABLE ozg_solution_audit DROP contact');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_solutions_communes')->hasColumn('solution_ready')) {
            $this->addSql('ALTER TABLE ozg_solutions_communes ADD solution_ready TINYINT(1) NOT NULL, ADD solution_ready_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        }
        if ($schema->hasTable('ozg_solutions_communes_contact')) {
            $this->addSql('DROP TABLE ozg_solutions_communes_contact');
        }
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_commune')->hasColumn('portal_interface_url')) {
            $this->addSql('ALTER TABLE ozg_commune DROP portal_interface_url');
            $this->addSql('ALTER TABLE ozg_commune_audit DROP portal_interface_url');
        }
        if (!$schema->getTable('ozg_commune')->hasColumn('contact')) {
            $this->addSql('ALTER TABLE ozg_commune ADD contact LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
            $this->addSql('ALTER TABLE ozg_commune_audit ADD contact LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        }
        if (!$schema->getTable('ozg_solution')->hasColumn('contact')) {
            $this->addSql('ALTER TABLE ozg_solution ADD contact LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
            $this->addSql('ALTER TABLE ozg_solution_audit ADD contact LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        }
    }
}
