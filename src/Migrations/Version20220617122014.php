<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617122014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert onboarding epayment number length fields to integer type';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE ozg_onboarding_epayment SET length_receipt_number = CAST(TRIM(REPLACE(length_receipt_number, 'Zeichen', '')) AS UNSIGNED) WHERE length_receipt_number IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment SET length_first_account_assignment_information = CAST(TRIM(REPLACE(length_first_account_assignment_information, 'Zeichen', '')) AS UNSIGNED) WHERE length_first_account_assignment_information IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment SET length_second_account_assignment_information = CAST(TRIM(REPLACE(length_second_account_assignment_information, 'Zeichen', '')) AS UNSIGNED) WHERE length_second_account_assignment_information IS NOT NULL");

        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_receipt_number = TRIM(REPLACE(length_receipt_number, 'Zeichen', '')) WHERE length_receipt_number IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_first_account_assignment_information = TRIM(REPLACE(length_first_account_assignment_information, 'Zeichen', '')) WHERE length_first_account_assignment_information IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_second_account_assignment_information = TRIM(REPLACE(length_second_account_assignment_information, 'Zeichen', '')) WHERE length_second_account_assignment_information IS NOT NULL");
        $this->addSql('UPDATE ozg_onboarding_epayment_audit SET length_first_account_assignment_information = NULL WHERE LENGTH(length_first_account_assignment_information) > 2');
        $this->addSql('UPDATE ozg_onboarding_epayment_audit SET length_receipt_number = NULL WHERE LENGTH(length_receipt_number) > 2');
        $this->addSql('UPDATE ozg_onboarding_epayment_audit SET length_second_account_assignment_information = NULL WHERE LENGTH(length_second_account_assignment_information) > 2');
        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_receipt_number = CAST(length_receipt_number AS UNSIGNED) WHERE length_receipt_number IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_first_account_assignment_information = CAST(length_first_account_assignment_information AS UNSIGNED) WHERE length_first_account_assignment_information IS NOT NULL");
        $this->addSql("UPDATE ozg_onboarding_epayment_audit SET length_second_account_assignment_information = CAST(length_second_account_assignment_information AS UNSIGNED) WHERE length_second_account_assignment_information IS NOT NULL");

        try {
            $this->addSql('ALTER TABLE ozg_onboarding_epayment CHANGE length_receipt_number length_receipt_number INT DEFAULT NULL, CHANGE length_first_account_assignment_information length_first_account_assignment_information INT DEFAULT NULL, CHANGE length_second_account_assignment_information length_second_account_assignment_information INT DEFAULT NULL');
            $this->addSql('ALTER TABLE ozg_onboarding_epayment_audit CHANGE length_receipt_number length_receipt_number INT DEFAULT NULL, CHANGE length_first_account_assignment_information length_first_account_assignment_information INT DEFAULT NULL, CHANGE length_second_account_assignment_information length_second_account_assignment_information INT DEFAULT NULL');
        } catch (\Exception $e) {
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ozg_onboarding_epayment CHANGE length_receipt_number length_receipt_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE length_first_account_assignment_information length_first_account_assignment_information VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE length_second_account_assignment_information length_second_account_assignment_information VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ozg_onboarding_epayment_audit CHANGE length_receipt_number length_receipt_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE length_first_account_assignment_information length_first_account_assignment_information VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE length_second_account_assignment_information length_second_account_assignment_information VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
