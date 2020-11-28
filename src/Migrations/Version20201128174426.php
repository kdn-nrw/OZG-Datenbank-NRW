<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201128174426 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_group_subgroup_mm (group_id INT NOT NULL, sub_group_id INT NOT NULL, INDEX IDX_B12FB7FFFE54D947 (group_id), INDEX IDX_B12FB7FF44FB371E (sub_group_id), PRIMARY KEY(group_id, sub_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_user_commune_mm (user_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_422CE798A76ED395 (user_id), INDEX IDX_422CE798131A4F72 (commune_id), PRIMARY KEY(user_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_user_model_region_mm (user_id INT NOT NULL, model_region_id INT NOT NULL, INDEX IDX_6E27B1EA76ED395 (user_id), INDEX IDX_6E27B1EA1EF68C6 (model_region_id), PRIMARY KEY(user_id, model_region_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_group_subgroup_mm ADD CONSTRAINT FK_B12FB7FFFE54D947 FOREIGN KEY (group_id) REFERENCES mb_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_group_subgroup_mm ADD CONSTRAINT FK_B12FB7FF44FB371E FOREIGN KEY (sub_group_id) REFERENCES mb_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_user_commune_mm ADD CONSTRAINT FK_422CE798A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_user_commune_mm ADD CONSTRAINT FK_422CE798131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_user_model_region_mm ADD CONSTRAINT FK_6E27B1EA76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_user_model_region_mm ADD CONSTRAINT FK_6E27B1EA1EF68C6 FOREIGN KEY (model_region_id) REFERENCES ozg_model_region (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ozg_group_subgroup_mm');
        $this->addSql('DROP TABLE ozg_user_commune_mm');
        $this->addSql('DROP TABLE ozg_user_model_region_mm');
    }
}
