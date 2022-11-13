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
        return 'Remove commune solution ready fields';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_solutions_communes')->hasColumn('solution_ready')) {
            $this->addSql('ALTER TABLE ozg_solutions_communes DROP solution_ready, DROP solution_ready_at');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_solutions_communes')->hasColumn('solution_ready')) {
            $this->addSql('ALTER TABLE ozg_solutions_communes ADD solution_ready TINYINT(1) NOT NULL, ADD solution_ready_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        }
    }
}
