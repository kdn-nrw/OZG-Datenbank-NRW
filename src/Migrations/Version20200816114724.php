<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200816114724 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_mailing_attachment (id INT AUTO_INCREMENT NOT NULL, mailing_id INT DEFAULT NULL, file_size INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_F419328F3931AB76 (mailing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_mailing_attachment ADD CONSTRAINT FK_F419328F3931AB76 FOREIGN KEY (mailing_id) REFERENCES ozg_mailing (id)');
        $this->addSql('ALTER TABLE ozg_mailing_attachment ADD local_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_contact ADD image_name VARCHAR(255) DEFAULT NULL, ADD image_size INT DEFAULT NULL, ADD url VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_subject ADD contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_subject ADD CONSTRAINT FK_9B662A40E7A1254A FOREIGN KEY (contact_id) REFERENCES ozg_contact (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9B662A40E7A1254A ON ozg_subject (contact_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ozg_mailing_attachment');
        $this->addSql('ALTER TABLE ozg_contact DROP image_name, DROP image_size, DROP url');
        $this->addSql('ALTER TABLE ozg_subject DROP FOREIGN KEY FK_9B662A40E7A1254A');
        $this->addSql('DROP INDEX IDX_9B662A40E7A1254A ON ozg_subject');
        $this->addSql('ALTER TABLE ozg_subject DROP contact_id');
    }
}
