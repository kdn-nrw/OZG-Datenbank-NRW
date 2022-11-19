<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210306090715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_implementation_project')->hasColumn('nationwide_rollout_at')) {
            $this->addSql('ALTER TABLE ozg_implementation_project ADD nationwide_rollout_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
            $this->addSql('INSERT INTO `ozg_implementation_status` (id, level, description, modified_at, created_at, name, hidden, next_status_id, set_automatically, prev_status_id, status_switch, color, css_class) VALUES (8, 5, null, \'2021-03-06 10:00:00\', \'2021-03-06 10:00:00\', \'Start bundesweiter Roll-out\', 0, null, 1, 4, null, \'#2f4814\', null)');
            $this->addSql('UPDATE `ozg_implementation_status` SET level = 6 WHERE id = 7');
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_implementation_project')->hasColumn('nationwide_rollout_at')) {
            $this->addSql('ALTER TABLE ozg_implementation_project DROP nationwide_rollout_at');
        }
    }
}
