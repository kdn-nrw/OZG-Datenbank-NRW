<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Import\DataProcessor;

use App\Entity\Base\BaseEntity;
use App\Entity\ModelRegionBeneficiary;
use App\Entity\ModelRegionProject;
use App\Entity\Organisation;
use App\Import\Annotation\ImportModelAnnotation;
use App\Import\Model\AbstractImportModel;

class ModelRegionProjectDataProcessor extends AbstractDataProcessor
{
    /**
     * @param string $importModelClass
     */
    public function setImportModelClass(string $importModelClass): void
    {
        parent::setImportModelClass($importModelClass);
        $objectManager = $this->getEntityManager();
        $callback = static function (string $value) use ($objectManager): array {
            $checkValues = explode(',', $value);
            $organisations = [];
            $repository = $objectManager->getRepository(Organisation::class);
            foreach ($checkValues as $checkVal) {
                if ('' !== $dbVal = trim(str_replace('Stadt', '', $checkVal))) {
                    $organisation = $repository->findOneBy(['name' => $dbVal]);
                    if (null === $organisation) {
                        $object = new ModelRegionBeneficiary();
                        $organisation = $object->getOrganisation();
                        $organisation->setName(trim($checkVal));
                        $organisation->setFromReference($object);
                        $objectManager->persist($object);
                        if (!$objectManager->contains($organisation)) {
                            $objectManager->persist($organisation);
                        }
                    } elseif (empty($organisation->getName())) {
                        $organisation->setName(trim($checkVal));
                    }
                    $organisations[$organisation->getId()] = $organisation;
                }
            }
            return $organisations;
        };
        $this->addCallback('organisations', $callback);
    }

    /**
     * @inheritDoc
     */
    protected function findOrCreateImportedEntity(string $entityClass, AbstractImportModel $importModel): BaseEntity
    {
        $targetEntity = parent::findOrCreateImportedEntity($entityClass, $importModel);
        if ($targetEntity instanceof ModelRegionProject) {
            if (null !== $projectEndAt = $targetEntity->getProjectEndAt()) {
                $projectEndAt->setTime(23, 59, 59);
            }
        }
        return $targetEntity;
    }
}
