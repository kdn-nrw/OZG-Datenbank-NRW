<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019102021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_application_interface (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_938490613E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_interface_specialized_procedures (application_interface_id INT NOT NULL, specialized_procedure_id INT NOT NULL, INDEX IDX_976F13CF81E22DB (application_interface_id), INDEX IDX_976F13CF452D2882 (specialized_procedure_id), PRIMARY KEY(application_interface_id, specialized_procedure_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_module (id INT AUTO_INCREMENT NOT NULL, application_id INT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_D70098A83E030ACD (application_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_application_interface ADD CONSTRAINT FK_938490613E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_application_interface_specialized_procedures ADD CONSTRAINT FK_976F13CF81E22DB FOREIGN KEY (application_interface_id) REFERENCES ozg_application_interface (id)');
        $this->addSql('ALTER TABLE ozg_application_interface_specialized_procedures ADD CONSTRAINT FK_976F13CF452D2882 FOREIGN KEY (specialized_procedure_id) REFERENCES ozg_specialized_procedure (id)');
        $this->addSql('ALTER TABLE ozg_application_module ADD CONSTRAINT FK_D70098A83E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_application_interface_specialized_procedures DROP FOREIGN KEY FK_976F13CF81E22DB');
        $this->addSql('DROP TABLE ozg_application_interface');
        $this->addSql('DROP TABLE ozg_application_interface_specialized_procedures');
        $this->addSql('DROP TABLE ozg_application_module');
    }
}
