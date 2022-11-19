<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200830120702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create slugs for entities used in the frontend';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $slugTables = [
            'ozg_implementation_project' => ["name"],
            'ozg_model_region' => ["name"],
            'ozg_model_region_project' => ["name"],
            'ozg_service' => ["name", "service_key"],
            'ozg_service_system' => ["name", "service_key"],
            'ozg_solution' => ["name"],
        ];
        foreach ($slugTables as $table => $slugFields) {
            $slugMap = [];
            $rows = $this->connection->fetchAll('SELECT id, '.implode(', ', $slugFields).' FROM ' . $table . ' ORDER BY id ASC');
            $this->addSql('ALTER TABLE '.$table.' ADD slug VARCHAR(128) NOT NULL');
            foreach ($rows as $row) {
                $slug = $this->getRowSlug($row, $slugMap);
                $id = (int) $row['id'];
                $this->addSql('UPDATE '.$table.' SET slug = \''.$slug.'\' WHERE id = ' . $id);
            }
        }
        $this->addSql('CREATE UNIQUE INDEX UNIQ_56C75D24989D9B62 ON ozg_implementation_project (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AAF9D279989D9B62 ON ozg_model_region (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DCB266A2989D9B62 ON ozg_model_region_project (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81358EE8989D9B62 ON ozg_service (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_18AF8D78989D9B62 ON ozg_service_system (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_595F587D989D9B62 ON ozg_solution (slug)');
    }

    /**
     * Create a unique slug for the given row
     *
     * @param array $row
     * @param array $slugMap The list of already generated slugs
     * @param int $maxLength The maximum length for the slug
     * @return string
     */
    private function getRowSlug(array $row, array &$slugMap, int $maxLength = 128): string
    {
        $id = $row['id'] ?? 0;
        unset($row['id']);
        $text = implode(' ', array_filter($row));
        $slug = $this->urlize($text);
        /** @noinspection NotOptimalIfConditionsInspection */
        if (in_array($slug, $slugMap, false) && $id) {
            $slug = $this->urlize($text. '-' . (int) $id);
        }
        if (in_array($slug, $slugMap, false)) {
            $slug = $this->urlize($text);
            $tmpSlug = $slug;
            if (strlen($tmpSlug) > $maxLength - 4) {
                $tmpSlug = substr($tmpSlug, 0, $maxLength - 4);
            }
            $offset = 1;
            while ($offset < 10000 && in_array($slug, $slugMap, false)) {
                $slug = $tmpSlug . '-' . $offset;
                ++$offset;
            }
        }
        $slugMap[] = $slug;
        return $slug;
    }

    /**
     * Create url from given text; crop to max length if necessary
     *
     * @param string $text
     * @param int $maxLength
     * @return string
     */
    private function urlize(string $text, int $maxLength = 128): string
    {
        $slug = Transliterator::urlize($text);
        if (strlen($slug) > $maxLength) {
            $slug = substr($slug, 0, $maxLength);
        }
        return $slug;
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_56C75D24989D9B62 ON ozg_implementation_project');
        $this->addSql('DROP INDEX UNIQ_AAF9D279989D9B62 ON ozg_model_region');
        $this->addSql('DROP INDEX UNIQ_DCB266A2989D9B62 ON ozg_model_region_project');
        $this->addSql('DROP INDEX UNIQ_81358EE8989D9B62 ON ozg_service');
        $this->addSql('DROP INDEX UNIQ_18AF8D78989D9B62 ON ozg_service_system');
        $this->addSql('DROP INDEX UNIQ_595F587D989D9B62 ON ozg_solution');
        $this->addSql('ALTER TABLE ozg_implementation_project DROP slug');
        $this->addSql('ALTER TABLE ozg_model_region DROP slug');
        $this->addSql('ALTER TABLE ozg_model_region_project DROP slug');
        $this->addSql('ALTER TABLE ozg_service DROP slug');
        $this->addSql('ALTER TABLE ozg_service_system DROP slug');
        $this->addSql('ALTER TABLE ozg_solution DROP slug');
    }
}
