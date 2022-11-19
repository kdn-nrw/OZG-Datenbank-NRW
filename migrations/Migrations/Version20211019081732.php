<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211019081732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_onboarding_commune_solution (id INT AUTO_INCREMENT NOT NULL, solution_id INT DEFAULT NULL, commune_id INT DEFAULT NULL, commune_info_id INT DEFAULT NULL, enabled_epayment TINYINT(1) NOT NULL, enabled_municipal_portal TINYINT(1) NOT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', position INT DEFAULT NULL, INDEX IDX_750215081C0BE183 (solution_id), INDEX IDX_75021508131A4F72 (commune_id), INDEX IDX_75021508BBB53111 (commune_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_onboarding_commune_solution ADD CONSTRAINT FK_750215081C0BE183 FOREIGN KEY (solution_id) REFERENCES ozg_solution (id)');
        $this->addSql('ALTER TABLE ozg_onboarding_commune_solution ADD CONSTRAINT FK_75021508131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_onboarding_commune_solution ADD CONSTRAINT FK_75021508BBB53111 FOREIGN KEY (commune_info_id) REFERENCES ozg_onboarding_base_info (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_onboarding_commune_solution');
    }
}
