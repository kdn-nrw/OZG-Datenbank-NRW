<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412083304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry ADD CONSTRAINT FK_F0E62D22727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_onboarding_inquiry (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F0E62D22727ACA70 ON ozg_onboarding_inquiry (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry DROP FOREIGN KEY FK_F0E62D22727ACA70');
        $this->addSql('DROP INDEX IDX_F0E62D22727ACA70 ON ozg_onboarding_inquiry');
        $this->addSql('ALTER TABLE ozg_onboarding_inquiry DROP parent_id');
    }
}
