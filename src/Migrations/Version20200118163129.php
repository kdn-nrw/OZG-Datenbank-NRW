<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200118163129 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_analog_service (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_solution_analog_service (solution_id INT NOT NULL, analog_service_id INT NOT NULL, INDEX IDX_880D50E51C0BE183 (solution_id), INDEX IDX_880D50E5E95BF44D (analog_service_id), PRIMARY KEY(solution_id, analog_service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_solution_open_data (solution_id INT NOT NULL, open_data_id INT NOT NULL, INDEX IDX_A9EDB3131C0BE183 (solution_id), INDEX IDX_A9EDB3135AC16DCF (open_data_id), PRIMARY KEY(solution_id, open_data_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_open_data (id INT AUTO_INCREMENT NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_solution_analog_service ADD CONSTRAINT FK_880D50E51C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_solution_analog_service ADD CONSTRAINT FK_880D50E5E95BF44D FOREIGN KEY (analog_service_id) REFERENCES ozg_analog_service (id)');
        $this->addSql('ALTER TABLE ozg_solution_open_data ADD CONSTRAINT FK_A9EDB3131C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_solution_open_data ADD CONSTRAINT FK_A9EDB3135AC16DCF FOREIGN KEY (open_data_id) REFERENCES ozg_open_data (id)');
        $this->addSql('INSERT INTO ozg_analog_service (name, hidden) VALUES (\'Terminvereinbarung\', 0)');
        $this->addSql('INSERT INTO ozg_open_data (name, hidden) VALUES (\'Geoinformationen\', 0)');
        $this->addSql('ALTER TABLE ozg_mailing ADD sender_name VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_solution_analog_service DROP FOREIGN KEY FK_880D50E5E95BF44D');
        $this->addSql('ALTER TABLE ozg_solution_open_data DROP FOREIGN KEY FK_A9EDB3135AC16DCF');
        $this->addSql('DROP TABLE ozg_analog_service');
        $this->addSql('DROP TABLE ozg_solution_analog_service');
        $this->addSql('DROP TABLE ozg_solution_open_data');
        $this->addSql('DROP TABLE ozg_open_data');
        $this->addSql('ALTER TABLE ozg_mailing DROP sender_name, DROP email');
    }
}
