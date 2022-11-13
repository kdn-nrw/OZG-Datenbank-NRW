<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221113154523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add commune portal interface url ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('ozg_commune')->hasColumn('portal_interface_url')) {
            $this->addSql('ALTER TABLE ozg_commune ADD portal_interface_url VARCHAR(2048) DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_commune_audit ADD portal_interface_url VARCHAR(2048) DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('ozg_commune')->hasColumn('portal_interface_url')) {
            $this->addSql('ALTER TABLE ozg_commune DROP portal_interface_url');
            $this->addSql('ALTER TABLE ozg_commune_audit DROP portal_interface_url');
        }
    }
}
