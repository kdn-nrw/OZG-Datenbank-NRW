<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191215111356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_C0206867727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_contact_category (contact_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_A3376298E7A1254A (contact_id), INDEX IDX_A337629812469DE2 (category_id), PRIMARY KEY(contact_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_category ADD CONSTRAINT FK_C0206867727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_category (id)');
        $this->addSql('ALTER TABLE ozg_contact_category ADD CONSTRAINT FK_A3376298E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id)');
        $this->addSql('ALTER TABLE ozg_contact_category ADD CONSTRAINT FK_A337629812469DE2 FOREIGN KEY (category_id) REFERENCES ozg_category (id)');
        $this->addSql('ALTER TABLE ozg_contact ADD contact_type VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_implementation_project ADD notes LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_category DROP FOREIGN KEY FK_C0206867727ACA70');
        $this->addSql('ALTER TABLE ozg_contact_category DROP FOREIGN KEY FK_A337629812469DE2');
        $this->addSql('DROP TABLE ozg_category');
        $this->addSql('DROP TABLE ozg_contact_category');
        $this->addSql('ALTER TABLE ozg_contact DROP contact_type');
        $this->addSql('ALTER TABLE ozg_implementation_project DROP notes');
    }
}
