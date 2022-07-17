<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220717151719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ozg_payment_platform (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT DEFAULT NULL, description LONGTEXT DEFAULT NULL, modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime)\', name VARCHAR(255) DEFAULT NULL, hidden TINYINT(1) NOT NULL, INDEX IDX_46CA756DA23B42D (manufacturer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ozg_payment_platform_commune (payment_platform_id INT NOT NULL, commune_id INT NOT NULL, INDEX IDX_4D4EA4E96A99B5 (payment_platform_id), INDEX IDX_4D4EA4E131A4F72 (commune_id), PRIMARY KEY(payment_platform_id, commune_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ozg_payment_platform ADD CONSTRAINT FK_46CA756DA23B42D FOREIGN KEY (manufacturer_id) REFERENCES ozg_manufacturer (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE ozg_payment_platform_commune ADD CONSTRAINT FK_4D4EA4E96A99B5 FOREIGN KEY (payment_platform_id) REFERENCES ozg_payment_platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ozg_payment_platform_commune ADD CONSTRAINT FK_4D4EA4E131A4F72 FOREIGN KEY (commune_id) REFERENCES ozg_commune (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_payment_platform_commune DROP FOREIGN KEY FK_4D4EA4E96A99B5');
        $this->addSql('DROP TABLE ozg_payment_platform');
        $this->addSql('DROP TABLE ozg_payment_platform_commune');
    }
}
