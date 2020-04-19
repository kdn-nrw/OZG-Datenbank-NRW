<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200419122752 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP FOREIGN KEY FK_E996869A1C0BE183');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP FOREIGN KEY FK_E996869A694459BB');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD id INT AUTO_INCREMENT NOT NULL, ADD status_id INT DEFAULT NULL, ADD article_number VARCHAR(255) DEFAULT NULL, ADD assistant_type VARCHAR(255) DEFAULT NULL, ADD article_key VARCHAR(255) DEFAULT NULL, ADD usable_as_print_template TINYINT(1) NOT NULL, ADD modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', ADD created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', CHANGE form_server_id form_server_id INT DEFAULT NULL, CHANGE solution_id solution_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD CONSTRAINT FK_E996869A6BF700BD FOREIGN KEY (status_id) REFERENCES ozg_status (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD CONSTRAINT FK_E996869A1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD CONSTRAINT FK_E996869A694459BB FOREIGN KEY (form_server_id) REFERENCES ozg_form_server (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_E996869A6BF700BD ON ozg_form_servers_solutions (status_id)');
        $this->addSql('ALTER TABLE ozg_solution ADD import_id INT DEFAULT NULL, ADD import_source VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_solution CHANGE name name VARCHAR(1024) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_form_servers_solutions MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP FOREIGN KEY FK_E996869A6BF700BD');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP FOREIGN KEY FK_E996869A694459BB');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP FOREIGN KEY FK_E996869A1C0BE183');
        $this->addSql('DROP INDEX IDX_E996869A6BF700BD ON ozg_form_servers_solutions');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions DROP id, DROP status_id, DROP article_number, DROP assistant_type, DROP article_key, DROP usable_as_print_template, DROP modified_at, DROP created_at, CHANGE form_server_id form_server_id INT NOT NULL, CHANGE solution_id solution_id INT NOT NULL');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD CONSTRAINT FK_E996869A694459BB FOREIGN KEY (form_server_id) REFERENCES ozg_form_server (id)');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD CONSTRAINT FK_E996869A1C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_form_servers_solutions ADD PRIMARY KEY (form_server_id, solution_id)');
        $this->addSql('ALTER TABLE ozg_solution DROP import_id, DROP import_source');
    }
}
