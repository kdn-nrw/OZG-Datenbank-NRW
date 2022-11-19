<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118220147 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->hasTable('ozg_meta_item')) {
            $this->addSql('CREATE TABLE ozg_meta_item (id INT AUTO_INCREMENT NOT NULL, meta_type VARCHAR(255) NOT NULL, meta_key VARCHAR(255) DEFAULT NULL, custom_label VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE ozg_meta_item_property (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, meta_type VARCHAR(255) NOT NULL, meta_key VARCHAR(255) DEFAULT NULL, custom_label VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_F6A14D58727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE ozg_meta_item_property ADD CONSTRAINT FK_F6A14D58727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_meta_item (id) ON DELETE CASCADE');
            $this->addSql('ALTER TABLE ozg_meta_item_property ADD internal_label VARCHAR(255) DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_meta_item ADD internal_label VARCHAR(255) DEFAULT NULL');
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_meta_item_property DROP FOREIGN KEY FK_F6A14D58727ACA70');
        $this->addSql('DROP TABLE ozg_meta_item');
        $this->addSql('DROP TABLE ozg_meta_item_property');
    }
}
