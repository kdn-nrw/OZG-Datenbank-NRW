<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190606130752 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM migration_versions WHERE version < ' . $this->version->getVersion());

        $this->addSql('ALTER TABLE mb_user_permissions DROP FOREIGN KEY FK_8068E14C25F94802');
        $this->addSql('ALTER TABLE mb_user_permissions DROP FOREIGN KEY FK_8068E14CDE12AB56');
        $this->addSql('ALTER TABLE mb_user_role DROP FOREIGN KEY FK_466E8BB825F94802');
        $this->addSql('ALTER TABLE mb_user_role DROP FOREIGN KEY FK_466E8BB8DE12AB56');
        $this->addSql('ALTER TABLE mb_user_user_role_mm DROP FOREIGN KEY FK_3174B559A76ED395');
        $this->addSql('ALTER TABLE mb_user_role_permission_mm DROP FOREIGN KEY FK_EFF8722AFED90CCA');
        $this->addSql('ALTER TABLE mb_user_role DROP FOREIGN KEY FK_466E8BB86A8ABCDE');
        $this->addSql('ALTER TABLE mb_user_role_permission_mm DROP FOREIGN KEY FK_EFF8722AD60322AC');
        $this->addSql('ALTER TABLE mb_user_user_role_mm DROP FOREIGN KEY FK_3174B559D60322AC');

        $this->addSql('ALTER TABLE mb_user RENAME TO mb_user_user');
        $this->addSql('ALTER TABLE mb_user_role RENAME TO mb_user_group');
        $this->addSql('ALTER TABLE mb_user_user_role_mm RENAME TO mb_user_user_group_mm');


        $this->addSql('DROP INDEX IDX_3174B559D60322AC ON mb_user_user_group_mm');
        $this->addSql('DROP INDEX UNIQ_466E8BB8E09C0C92 ON mb_user_group');
        $this->addSql('DROP INDEX IDX_466E8BB8DE12AB56 ON mb_user_group');
        $this->addSql('DROP INDEX IDX_466E8BB86A8ABCDE ON mb_user_group');
        $this->addSql('DROP INDEX IDX_466E8BB825F94802 ON mb_user_group');

        $this->addSql('ALTER TABLE mb_user_user CHANGE modified_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_user CHANGE salutation gender VARCHAR(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_user CHANGE first_name firstname VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_user CHANGE last_name lastname VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_group CHANGE role_name name VARCHAR(180) NOT NULL');

        $this->addSql('UPDATE mb_user_user SET created_at = NOW() WHERE created_at IS NULL');
        $this->addSql('UPDATE mb_user_user SET updated_at = NOW() WHERE updated_at IS NULL');

        $this->addSql('ALTER TABLE mb_user_user ADD date_of_birth DATETIME DEFAULT NULL, ADD website VARCHAR(64) DEFAULT NULL, ADD biography VARCHAR(1000) DEFAULT NULL, ADD locale VARCHAR(8) DEFAULT NULL, ADD timezone VARCHAR(64) DEFAULT NULL, ADD phone VARCHAR(64) DEFAULT NULL, ADD facebook_name VARCHAR(255) DEFAULT NULL, ADD facebook_data JSON DEFAULT NULL, ADD twitter_uid VARCHAR(255) DEFAULT NULL, ADD twitter_name VARCHAR(255) DEFAULT NULL, ADD twitter_data JSON DEFAULT NULL, ADD gplus_uid VARCHAR(255) DEFAULT NULL, ADD gplus_name VARCHAR(255) DEFAULT NULL, ADD gplus_data JSON DEFAULT NULL, ADD token VARCHAR(255) DEFAULT NULL, ADD two_step_code VARCHAR(255) DEFAULT NULL, DROP deleted, DROP expired, DROP locked, DROP expires_at, DROP deleted_at, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE title facebook_uid VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE mb_user_group ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP parent_role, DROP created_by, DROP modified_by, DROP display_name, DROP modified_at, DROP created_at, DROP deleted_at');

        $this->addSql('UPDATE mb_user_group SET roles = "a:0:{}" WHERE roles LIKE ""');

        $this->addSql('ALTER TABLE mb_user_user RENAME INDEX uniq_24a50d4592fc23a8 TO UNIQ_9C94D79B92FC23A8');
        $this->addSql('ALTER TABLE mb_user_user RENAME INDEX uniq_24a50d45a0d96fbf TO UNIQ_9C94D79BA0D96FBF');
        $this->addSql('ALTER TABLE mb_user_user RENAME INDEX uniq_24a50d45c05fb297 TO UNIQ_9C94D79BC05FB297');
        $this->addSql('ALTER TABLE mb_user_user_group_mm DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mb_user_user_group_mm CHANGE role_id group_id INT NOT NULL');
        $this->addSql('ALTER TABLE mb_user_user_group_mm ADD CONSTRAINT FK_F81FC676A76ED395 FOREIGN KEY (user_id) REFERENCES mb_user_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mb_user_user_group_mm ADD CONSTRAINT FK_F81FC676FE54D947 FOREIGN KEY (group_id) REFERENCES mb_user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mb_user_user_group_mm ADD PRIMARY KEY (user_id, group_id)');
        $this->addSql('ALTER TABLE mb_user_user_group_mm RENAME INDEX idx_3174b559a76ed395 TO IDX_F81FC676A76ED395');
        $this->addSql('CREATE INDEX IDX_F81FC676FE54D947 ON mb_user_user_group_mm (group_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_50CF03C5E237E06 ON mb_user_group (name)');

    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException('Major upgrades are not reversible');
    }
}
