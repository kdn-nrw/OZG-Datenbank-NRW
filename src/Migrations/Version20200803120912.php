<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200803120912 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ozg_application (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, accessibility LONGTEXT DEFAULT NULL, privacy LONGTEXT DEFAULT NULL, archive LONGTEXT DEFAULT NULL, in_house_development TINYINT(1) DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, street VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(20) DEFAULT NULL, town VARCHAR(255) DEFAULT NULL, url VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_commune (application_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_F8176703E030ACD (application_id), INDEX IDX_F817670131A4F72 (commune_id), PRIMARY KEY(application_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_manufacturer (application_id INT NOT NULL, manufacturer_id INT NOT NULL, INDEX IDX_22C1A6C93E030ACD (application_id), INDEX IDX_22C1A6C9A23B42D (manufacturer_id), PRIMARY KEY(application_id, manufacturer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_service_provider (application_id INT NOT NULL, service_provider_id INT NOT NULL, INDEX IDX_D9E901BC3E030ACD (application_id), INDEX IDX_D9E901BCC6C98E06 (service_provider_id), PRIMARY KEY(application_id, service_provider_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_application_category (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, position INT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, import_id INT DEFAULT NULL, import_source VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_application_commune ADD CONSTRAINT FK_F8176703E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_application_commune ADD CONSTRAINT FK_F817670131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_application_manufacturer ADD CONSTRAINT FK_22C1A6C93E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_application_manufacturer ADD CONSTRAINT FK_22C1A6C9A23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_application_service_provider ADD CONSTRAINT FK_D9E901BC3E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id)');
        $this->addSql('ALTER TABLE ozg_application_service_provider ADD CONSTRAINT FK_D9E901BCC6C98E06 FOREIGN KEY (service_provider_id) REFERENCES ozg_service_provider (id)');
        $this->addSql('CREATE TABLE ozg_application_category_mm (application_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_1E359B913E030ACD (application_id), INDEX IDX_1E359B9112469DE2 (category_id), PRIMARY KEY(application_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_application_category_mm ADD CONSTRAINT FK_1E359B913E030ACD FOREIGN KEY (application_id) REFERENCES ozg_application (id)');
        $this->addSql('ALTER TABLE ozg_application_category_mm ADD CONSTRAINT FK_1E359B9112469DE2 FOREIGN KEY (category_id) REFERENCES ozg_application_category (id)');
        $this->addSql('ALTER TABLE ozg_application_category ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ozg_application_category ADD CONSTRAINT FK_1116C425727ACA70 FOREIGN KEY (parent_id) REFERENCES ozg_application_category (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_1116C425727ACA70 ON ozg_application_category (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ozg_application_commune DROP FOREIGN KEY FK_F8176703E030ACD');
        $this->addSql('ALTER TABLE ozg_application_manufacturer DROP FOREIGN KEY FK_22C1A6C93E030ACD');
        $this->addSql('ALTER TABLE ozg_application_service_provider DROP FOREIGN KEY FK_D9E901BC3E030ACD');
        $this->addSql('DROP TABLE ozg_application');
        $this->addSql('DROP TABLE ozg_application_commune');
        $this->addSql('DROP TABLE ozg_application_manufacturer');
        $this->addSql('DROP TABLE ozg_application_service_provider');
        $this->addSql('DROP TABLE ozg_application_category');
        $this->addSql('DROP TABLE ozg_application_category_mm');
        $this->addSql('ALTER TABLE ozg_application_category DROP FOREIGN KEY FK_1116C425727ACA70');
        $this->addSql('DROP INDEX IDX_1116C425727ACA70 ON ozg_application_category');
        $this->addSql('ALTER TABLE ozg_application_category DROP parent_id');
    }
}
