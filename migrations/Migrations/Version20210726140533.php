<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210726140533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add commune solution fields';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_solutions_communes ADD id INT AUTO_INCREMENT NOT NULL, ADD specialized_procedure_id INT DEFAULT NULL, ADD commune_type VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD solution_ready TINYINT(1) NOT NULL, ADD solution_ready_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD connection_planned TINYINT(1) NOT NULL, ADD connection_planned_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD comment LONGTEXT DEFAULT NULL, ADD modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD name VARCHAR(255) DEFAULT NULL, ADD hidden TINYINT(1) NOT NULL, CHANGE solution_id solution_id INT DEFAULT NULL, CHANGE commune_id commune_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE ozg_solutions_communes ADD CONSTRAINT FK_9A1545CE452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9A1545CE452D2882 ON ozg_solutions_communes (specialized_procedure_id)');
        $this->addSql('UPDATE ozg_solutions_communes SET commune_type = \'selected\' WHERE solution_id > 0');
        $this->addSql('UPDATE ozg_solutions_communes sc, ozg_solution s, ozg_status st SET sc.solution_ready = 1 WHERE s.id = sc.solution_id AND s.status_id = st.id AND st.name NOT LIKE \'%offline%\'');
        $this->addSql('ALTER TABLE ozg_solutions_communes CHANGE connection_planned connection_planned TINYINT(1) DEFAULT NULL;');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_solutions_communes MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_solutions_communes DROP FOREIGN KEY FK_9A1545CE452D2882');
        $this->addSql('DROP INDEX IDX_9A1545CE452D2882 ON ozg_solutions_communes');
        $this->addSql('ALTER TABLE ozg_solutions_communes DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ozg_solutions_communes DROP id, DROP specialized_procedure_id, DROP commune_type, DROP description, DROP solution_ready, DROP solution_ready_at, DROP connection_planned, DROP connection_planned_at, DROP comment, DROP modified_at, DROP created_at, DROP name, DROP hidden, CHANGE commune_id commune_id INT NOT NULL, CHANGE solution_id solution_id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_solutions_communes ADD PRIMARY KEY (solution_id, commune_id)');
    }
}
