<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Configuration;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Configuration\CustomField;

class CustomFieldManager
{
    use InjectManagerRegistryTrait;

    /**
     * @param string $entityClass
     * @return CustomField[]|array
     */
    public function getCustomFieldsForRecordType(string $entityClass): array
    {
        $repository = $this->getEntityManager()->getRepository(CustomField::class);
        $customFields = $repository->findBy(
            ['recordType' => $entityClass, 'hidden' => false],
            ['position' => 'ASC', 'id' => 'ASC']
        );
        return $customFields;
    }
}