<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Import;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Base\PersonInterface;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\ImportEntityInterface;

/**
 * Import contacts from cms
 */
class CmsContactImport
{
    public const IMPORT_SOURCE = 'cms_contacts';

    use InjectManagerRegistryTrait;

    public function import(): void
    {
        $mapCategories = $this->importCategories();
        $groupedContactCategories = $this->getGroupedContactCategories($mapCategories);
        $this->importContacts($groupedContactCategories);
    }

    private function initExistingContactsEmailMap()
    {
        $existingContactsEmailMapping = [];

        $query = $this->getEntityManager()->createQueryBuilder();
        $query
            ->select(['c.id', 'c.email'])
            ->from(Contact::class, 'c')
            ->where('c.email IS NOT NULL')
            ->andWhere('c.hidden = 0')
            ->orderBy('c.id', 'ASC');
        $results = $query->getQuery()->getResult();
        $nonUniqueEmails = [];
        foreach ($results as $result) {
            $checkEmail = strtolower($result['email']);
            if ($checkEmail) {
                if (!isset($existingContactsEmailMapping[$checkEmail])) {
                    $existingContactsEmailMapping[$checkEmail] = [];
                }
                $existingContactsEmailMapping[$checkEmail][] = (int) $result['id'];
                if (1 < count($existingContactsEmailMapping[$checkEmail])) {
                    $nonUniqueEmails[$checkEmail] = $existingContactsEmailMapping[$checkEmail];
                }
            }
        }
        $this->removeDuplicates($nonUniqueEmails);
        return $existingContactsEmailMapping;
    }

    /**
     * Remove duplicate contacts
     * @param array $nonUniqueEmails
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    private function removeDuplicates(array $nonUniqueEmails)
    {
        if (empty($nonUniqueEmails)) {
            return;
        }
        /** @var \Doctrine\DBAL\Connection $remoteConnection */
        $localConnection = $this->registry->getConnection();
        $keepIds = [];
        foreach ($nonUniqueEmails as $contactIds) {
            $keepIds[] = $contactIds[0];
        }

        $copyFields = [
            'organisation_id' => 'a.organisation_id IS NULL AND b.organisation_id IS NOT NULL',
            'phone_number' => 'a.phone_number IS NULL AND b.phone_number IS NOT NULL',
            'zip_code' => 'a.zip_code IS NULL AND b.zip_code IS NOT NULL',
            'town' => 'a.town IS NULL AND b.town IS NOT NULL',
            'street' => 'a.street IS NULL AND b.street IS NOT NULL',
            'organisation' => 'a.organisation IS NULL AND b.organisation IS NOT NULL',
            'position' => 'a.position IS NULL AND b.position IS NOT NULL',
            'department' => 'a.department IS NULL AND b.department IS NOT NULL',
            'title' => 'a.title IS NULL AND b.title IS NOT NULL',
            'mobile_number' => 'a.mobile_number IS NULL AND b.mobile_number IS NOT NULL',
            'url' => 'a.url IS NULL AND b.url IS NOT NULL',
        ];
        foreach ($copyFields as $field => $fieldCondition) {
            $sql = 'UPDATE ozg_contact a, ozg_contact b SET a.'.$field.' = b.'.$field.' 
WHERE a.email = b.email AND a.id < b.id AND a.id IN ('.implode(',', $keepIds).') AND ' . $fieldCondition;
            $localConnection->executeStatement($sql);
        }
        $deleteTables = [
            'ozg_mailing_contact' => 'contact_id',
            'ozg_mailing_exclude_contact' => 'contact_id',
        ];
        $updateTables = [
            'ozg_contact_category' => [
                'local_id_field' => 'contact_id',
                'foreign_id_field' => 'category_id',
            ],
            'ozg_implementation_project_contact' => [
                'local_id_field' => 'contact_id',
                'foreign_id_field' => 'implementation_project_id',
            ],
            'ozg_implementation_project_fim_export' => [
                'local_id_field' => 'contact_id',
                'foreign_id_field' => 'implementation_project_id',
            ],
            'ozg_solution_contact' => [
                'local_id_field' => 'contact_id',
                'foreign_id_field' => 'solution_id',
            ],
        ];
        foreach ($nonUniqueEmails as $email => $contactIds) {
            $firstId = $contactIds[0];
            $mapTableValues = [];
            foreach ($updateTables as $table => $fieldsConfig) {
                $localField = $fieldsConfig['local_id_field'];
                $foreignField = $fieldsConfig['foreign_id_field'];
                $sql = 'SELECT '.$foreignField.' FROM '.$table.' WHERE '.$localField.' = ' . $firstId;
                $mapTableValues[$table] = $localConnection->executeQuery($sql)->fetchFirstColumn();
            }
            for ($i = 1, $n = count($contactIds); $i < $n; $i++) {
                $mapId = $contactIds[$i];
                foreach ($deleteTables as $table => $field) {
                    $sql = 'DELETE FROM '.$table.' WHERE '.$field.' = ' . $mapId;
                    $localConnection->executeStatement($sql);
                }
                foreach ($updateTables as $table => $fieldsConfig) {
                    $localField = $fieldsConfig['local_id_field'];
                    $foreignField = $fieldsConfig['foreign_id_field'];
                    if (!empty($mapTableValues[$table])) {
                        $sql = 'UPDATE '.$table.' SET '.$localField.' = ' . $firstId
                            . ' WHERE '.$localField.' = ' . $mapId
                            . ' AND ' . $foreignField . ' NOT IN ('.implode(',', $mapTableValues[$table]).')';
                        $localConnection->executeStatement($sql);
                        $sql = 'SELECT '.$foreignField.' FROM '.$table.' WHERE '.$localField.' = ' . $firstId;
                        $mapTableValues[$table] = $localConnection->executeQuery($sql)->fetchFirstColumn();
                    }
                    $sql = 'DELETE FROM '.$table.' WHERE '.$localField.' = ' . $mapId;
                    $localConnection->executeStatement($sql);
                }
                $sql = 'DELETE FROM ozg_contact WHERE id = ' . $mapId;
                $localConnection->executeStatement($sql);
            }
        }
    }

    private function importContacts(array $groupedContactCategories): array
    {
        $em = $this->getEntityManager();
        /** @var \Doctrine\DBAL\Connection $remoteConnection */
        $remoteConnection = $this->registry->getConnection('cms');
        if ((null !== $schemaManager = $remoteConnection->getSchemaManager()) && !$schemaManager->tablesExist(['tt_address'])) {
            return [];
        }
        $existingContactsEmailMapping = $this->initExistingContactsEmailMap();
        $sql = 'SELECT * FROM tt_address WHERE deleted = 0 AND hidden = 0 ORDER BY uid ASC';
        /** @noinspection PhpUnhandledExceptionInspection */
        $stmt = $remoteConnection->executeQuery($sql);
        $mapObjects = $this->getMappedObjects(Contact::class);
        $rowCount = 0;
        $importedIds = [];
        while ($row = $stmt->fetch()) {
            // Skip import if contact with same email already exists
            $checkEmail = strtolower($row['email']);
            if (!$checkEmail || isset($existingContactsEmailMapping[$checkEmail])) {
                continue;
            }
            /** @noinspection PhpUnhandledExceptionInspection */
            $importObject = $this->getLocalObjectForRemote($mapObjects, $row, Contact::class);
            /** @var Contact $importObject */
            $remoteObjectId = (int)$row['uid'];
            if (isset($groupedContactCategories[$remoteObjectId])) {
                $contactCategories = $groupedContactCategories[$remoteObjectId];
                $storedCategories = $importObject->getCategories();
                foreach ($storedCategories as $category) {
                    if ($category->getImportId() && !isset($contactCategories[$category->getId()])) {
                        $importObject->removeCategory($category);
                    }
                }
                foreach ($contactCategories as $contactCategory) {
                    $importObject->addCategory($contactCategory);
                }
            }
            if (!$importObject->getId()) {
                $em->persist($importObject);
            }
            ++$rowCount;
            $importedIds[] = $row['uid'];
            if ($rowCount > 50) {
                $em->flush();
                $rowCount = 0;
                $sql = 'UPDATE tt_address SET hidden = 1 WHERE uid IN (' . implode(',', $importedIds) . ')';
                $remoteConnection->executeStatement($sql);
                $importedIds = [];
            }
        }
        if (!empty($importedIds)) {
            $sql = 'UPDATE tt_address SET hidden = 1 WHERE uid IN (' . implode(',', $importedIds) . ')';
            $remoteConnection->executeStatement($sql);
        }
        $em->flush();
        //$this->deleteUnmatchedItems($mapObjects);

        return $mapObjects;
    }

    private function getGroupedContactCategories(array $mapCategories): array
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->registry->getConnection('cms');
        $sql = 'SELECT uid_local, uid_foreign FROM sys_category_record_mm WHERE tablenames = \'tt_address\' AND fieldname = \'categories\' ORDER BY sorting_foreign ASC';
        /** @noinspection PhpUnhandledExceptionInspection */
        $stmt = $connection->query($sql);
        $mapContactCategories = [];
        while ($row = $stmt->fetch()) {
            $remoteCategoryId = (int)$row['uid_local'];
            $remoteContactId = (int)$row['uid_foreign'];
            if (isset($mapCategories[$remoteCategoryId]) && $mapCategories[$remoteCategoryId]['found']) {
                $category = $mapCategories[$remoteCategoryId]['object'];
                /** @var Category $category */
                $mapContactCategories[$remoteContactId][$category->getId()] = $category;
            }
        }
        return $mapContactCategories;
    }

    private function importCategories(): array
    {
        $em = $this->getEntityManager();
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->registry->getConnection('cms');
        if ((null !== $schemaManager = $connection->getSchemaManager()) && !$schemaManager->tablesExist(['sys_category'])) {
            return [];
        }
        $sql = 'SELECT * FROM sys_category WHERE deleted = 0 AND hidden = 0 ORDER BY uid ASC';
        /** @noinspection PhpUnhandledExceptionInspection */
        $stmt = $connection->query($sql);
        $mapObjects = $this->getMappedObjects(Category::class);
        $rowCount = 0;
        while ($row = $stmt->fetch()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $importObject = $this->getLocalObjectForRemote($mapObjects, $row, Category::class);
            if (!$importObject->getId()) {
                $em->persist($importObject);
            }
            if ($rowCount > 50) {
                $em->flush();
                $rowCount = 0;
            }
            ++$rowCount;
        }
        $em->flush();
        $this->deleteUnmatchedItems($mapObjects);
        return $mapObjects;
    }

    private function deleteUnmatchedItems($mapObjects): void
    {

        $em = $this->getEntityManager();
        foreach ($mapObjects as $map) {
            if (!$map['found']) {
                $em->remove($map['object']);
            }
        }
        $em->flush();
    }

    private function getLocalObjectForRemote(&$mapObjects, array $row, string $entityClass)
    {
        $remoteObjectId = (int)$row['uid'];
        if (isset($mapObjects[$remoteObjectId])) {
            $importObject = $mapObjects[$remoteObjectId]['object'];
            $mapObjects[$remoteObjectId]['found'] = true;
        } else {
            switch ($entityClass) {
                case Category::class:
                    $importObject = new Category();
                    break;
                case Contact::class:
                    $importObject = new Contact();
                    break;
                default:
                    throw new \Exception('Entity class ' . $entityClass . ' not valid for import');
            }
            $mapObjects[$remoteObjectId] = [
                'object' => $importObject,
                'found' => true,
                'is_new' => true,
            ];
            $importObject->setImportId($remoteObjectId);
            $importObject->setImportSource(self::IMPORT_SOURCE);
            if (!empty($row['crdate'])) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $importObject->setCreatedAt(new \DateTime('@' . $row['crdate']));
            }
        }
        switch ($entityClass) {
            case Category::class:
                /** @var Category $importObject */
                $importObject->setName($row['title']);
                $importObject->setDescription($row['description']);
                $remoteParentId = (int)$row['parent'];
                if ($remoteParentId && isset($mapObjects[$remoteParentId])
                    && $mapObjects[$remoteParentId]['found']) {
                    $parentCategory = $mapObjects[$remoteParentId]['object'];
                    $importObject->setParent($parentCategory);
                }
                break;
            case Contact::class:
                /** @var Contact $importObject */
                $importObject->setEmail($row['email']);
                $importObject->setTitle($row['title']);
                $importObject->setFirstName($row['first_name']);
                $importObject->setLastName($row['last_name']);
                $importObject->setOrganisation($row['company']);
                $importObject->setZipCode($row['zip']);
                $importObject->setTown($row['city']);
                $importObject->setStreet($row['address']);
                $importObject->setPosition($row['position']);
                $importObject->setPhoneNumber($row['phone']);
                $importObject->setMobileNumber($row['mobile']);
                $importObject->setFaxNumber($row['fax']);
                if ($importObject->getContactType() === Contact::CONTACT_TYPE_DEFAULT) {
                    $importObject->setContactType(Contact::CONTACT_TYPE_IMPORT_CMS);
                }
                $gender = PersonInterface::GENDER_UNKNOWN;
                $rowGender = (string)$row['gender'];
                if ($rowGender === 'f') {
                    $gender = PersonInterface::GENDER_FEMALE;
                } elseif ($rowGender === 'm') {
                    $gender = PersonInterface::GENDER_MALE;
                } elseif ($rowGender === 'v') {
                    $gender = PersonInterface::GENDER_OTHER;
                }
                $importObject->setGender($gender);
                break;
            default:
                throw new \Exception('Entity class ' . $entityClass . ' not valid for import');
        }
        return $importObject;
    }

    private function getMappedObjects($entityClass): array
    {
        $em = $this->getEntityManager();
        /** @var ImportEntityInterface[] $localObjects */
        $repository = $em->getRepository($entityClass);
        $localObjects = $repository->findBy(['importSource' => self::IMPORT_SOURCE]);
        $mapObjects = [];
        foreach ($localObjects as $object) {
            $mapObjects[$object->getImportId()] = [
                'object' => $object,
                'found' => false,
                'is_new' => false,
            ];
        }
        return $mapObjects;
    }
}