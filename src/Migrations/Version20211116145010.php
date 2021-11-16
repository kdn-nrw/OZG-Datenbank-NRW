<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211116145010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_model_region_project_category_mm (project_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_5D7CD69166D1F9C (project_id), INDEX IDX_5D7CD6912469DE2 (category_id), PRIMARY KEY(project_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_model_region_project_category (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_model_region_project_category_mm ADD CONSTRAINT FK_5D7CD69166D1F9C FOREIGN KEY (project_id) REFERENCES ozg_model_region_project (id)');
        $this->addSql('ALTER TABLE ozg_model_region_project_category_mm ADD CONSTRAINT FK_5D7CD6912469DE2 FOREIGN KEY (category_id) REFERENCES ozg_model_region_project_category (id)');
        $categories = [
            'E-Government',
            'Weitere E-Government (-Lösungen)',
            'Open Government',
            'Prozesssteuerung',
            'Mobilität',
            'Freizeit',
            'Bildung',
            'Energie und Klima',
            'Gesundheit und Rettungsdienst',
            'Tourismus',
            'Einzelhandel',
            'Weitere Smart-City (-Lösungen)',
        ];
        foreach ($categories as $categoryName) {
            $this->addSql("INSERT INTO ozg_model_region_project_category (name, hidden) VALUES ('$categoryName', 0)");
        }
        $this->addSql("UPDATE ozg_model_region_project_category SET created_at = NOW(), modified_at = created_at WHERE created_at IS NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_model_region_project_category_mm DROP FOREIGN KEY FK_5D7CD6912469DE2');
        $this->addSql('DROP TABLE ozg_model_region_project_category_mm');
        $this->addSql('DROP TABLE ozg_model_region_project_category');
    }
}
