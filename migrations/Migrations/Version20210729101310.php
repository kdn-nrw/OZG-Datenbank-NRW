<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210729101310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add service priority with current values from service systems';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_service ADD priority_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_service ADD CONSTRAINT FK_81358EE8497B19F9 FOREIGN KEY (priority_id) REFERENCES ozg_priority (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_81358EE8497B19F9 ON ozg_service (priority_id)');
        $this->addSql('UPDATE ozg_service s, ozg_service_system st SET s.priority_id = st.priority_id WHERE s.service_system_id = st.id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_service DROP FOREIGN KEY FK_81358EE8497B19F9');
        $this->addSql('DROP INDEX IDX_81358EE8497B19F9 ON ozg_service');
        $this->addSql('ALTER TABLE ozg_service DROP priority_id');
    }
}
