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

use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\ImportEntityInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Import contacts from cms
 */
class CmsContactImport
{
    const IMPORT_SOURCE = 'cms_contacts';
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry|ManagerRegistry
     */
    private $registry;

    /**
     * @param \Doctrine\Common\Persistence\ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function import(): void
    {
        $mapCategories = $this->importCategories();
        $groupedContactCategories = $this->getGroupedContactCategories($mapCategories);
        $this->importContacts($groupedContactCategories);
    }

    private function importContacts(array $groupedContactCategories)
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->registry->getConnection('cms');
        $em = $this->registry->getManager();
        $sql = 'SELECT * FROM typo3kdn.tt_address WHERE deleted = 0 AND hidden = 0 ORDER BY uid ASC';
        /** @noinspection PhpUnhandledExceptionInspection */
        $stmt = $connection->query($sql);
        $mapObjects = $this->getMappedObjects(Contact::class);
        $rowCount = 0;
        $importedIds = [];
        while ($row = $stmt->fetch()) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $importObject = $this->getLocalObjectForRemote($mapObjects, $row, Contact::class);
            /** @var Contact $importObject */
            $remoteObjectId = (int) $row['uid'];
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
                $sql = 'UPDATE typo3kdn.tt_address SET hidden = 1 WHERE uid IN ('.implode(',', $importedIds).')';
                $connection->executeUpdate($sql);
                $importedIds = [];
            }
        }
        if (!empty($importedIds)) {
            $sql = 'UPDATE typo3kdn.tt_address SET hidden = 1 WHERE uid IN ('.implode(',', $importedIds).')';
            $connection->executeUpdate($sql);
        }
        $em->flush();
        //$this->deleteUnmatchedItems($mapObjects);

        return $mapObjects;
    }

    private function getGroupedContactCategories(array $mapCategories)
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->registry->getConnection('cms');
        $sql = 'SELECT uid_local, uid_foreign FROM typo3kdn.sys_category_record_mm WHERE tablenames = \'tt_address\' AND fieldname = \'categories\' ORDER BY sorting_foreign ASC';
        /** @noinspection PhpUnhandledExceptionInspection */
        $stmt = $connection->query($sql);
        $mapContactCategories = [];
        while ($row = $stmt->fetch()) {
            $remoteCategoryId = (int) $row['uid_local'];
            $remoteContactId = (int) $row['uid_foreign'];
            if (isset($mapCategories[$remoteCategoryId]) && $mapCategories[$remoteCategoryId]['found']) {
                $category = $mapCategories[$remoteCategoryId]['object'];
                /** @var Category $category */
                $mapContactCategories[$remoteContactId][$category->getId()] = $category;
            }
        }
        return $mapContactCategories;
    }

    private function importCategories()
    {
        $em = $this->registry->getManager();
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->registry->getConnection('cms');
        $sql = 'SELECT * FROM typo3kdn.sys_category WHERE deleted = 0 AND hidden = 0 ORDER BY uid ASC';
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

    private function deleteUnmatchedItems($mapObjects)
    {

        $em = $this->registry->getManager();
        foreach ($mapObjects as $map) {
            if (!$map['found']) {
                $em->remove($map['object']);
            }
        }
        $em->flush();
    }

    private function getLocalObjectForRemote(&$mapObjects, array $row, string $entityClass)
    {
        $remoteObjectId = (int) $row['uid'];
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
            $importObject->setImportId((int) $remoteObjectId);
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
                $remoteParentId = (int) $row['parent'];
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
                $gender = Contact::GENDER_UNKNOWN;
                if ($row['gender'] == 'f') {
                    $gender = Contact::GENDER_FEMALE;
                } elseif ($row['gender'] == 'm') {
                    $gender = Contact::GENDER_MALE;
                } elseif ($row['gender'] == 'v') {
                    $gender = Contact::GENDER_OTHER;
                }
                $importObject->setGender($gender);
                break;
            default:
                throw new \Exception('Entity class ' . $entityClass . ' not valid for import');
        }
        return $importObject;
    }

    private function getMappedObjects($entityClass)
    {
        $em = $this->registry->getManager();
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