<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201024063929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_implementation_project ADD concept_status_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD implementation_status_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD commissioning_status_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\'');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD next_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD CONSTRAINT FK_D3350B152C7DD58E FOREIGN KEY (next_status_id) REFERENCES ozg_implementation_status (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D3350B152C7DD58E ON ozg_implementation_status (next_status_id)');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD set_automatically TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD prev_status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD CONSTRAINT FK_D3350B1582FB4933 FOREIGN KEY (prev_status_id) REFERENCES ozg_implementation_status (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_D3350B1582FB4933 ON ozg_implementation_status (prev_status_id)');
        $this->addSql('ALTER TABLE ozg_implementation_status ADD status_switch INT DEFAULT NULL');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = 2, set_automatically = 0, prev_status_id = NULL, status_switch = 1 WHERE id = 1');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = 3, set_automatically = 1, prev_status_id = NULL, status_switch = 2 WHERE id = 2');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = 4, set_automatically = 1, prev_status_id = 2, status_switch = 3 WHERE id = 3');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = 6, set_automatically = 1, prev_status_id = 3, status_switch = 4 WHERE id = 4');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = 6, set_automatically = 0, prev_status_id = NULL, status_switch = 4 WHERE id = 5');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = NULL, set_automatically = 1, prev_status_id = 4, status_switch = NULL, description = \'Abgeschlossen und in Regelbetrieb überführt\', name = \'Inbetriebnahme\' WHERE id = 6');
        $this->addSql('UPDATE `ozg_implementation_status` SET next_status_id = NULL, set_automatically = 1, prev_status_id = NULL, status_switch = 1, level = 9 WHERE id = 7');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_implementation_status DROP FOREIGN KEY FK_D3350B152C7DD58E');
        $this->addSql('DROP INDEX IDX_D3350B152C7DD58E ON ozg_implementation_status');
        $this->addSql('ALTER TABLE ozg_implementation_status DROP next_status_id');
        $this->addSql('ALTER TABLE ozg_implementation_status DROP set_automatically');
        $this->addSql('ALTER TABLE ozg_implementation_status DROP FOREIGN KEY FK_D3350B1582FB4933');
        $this->addSql('DROP INDEX IDX_D3350B1582FB4933 ON ozg_implementation_status');
        $this->addSql('ALTER TABLE ozg_implementation_status DROP prev_status_id');
        $this->addSql('ALTER TABLE ozg_implementation_project DROP concept_status_at, DROP implementation_status_at, DROP commissioning_status_at');
    }
}
