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

namespace App\Form\DataMapper;

use App\Entity\Configuration\CustomField;
use App\Entity\Configuration\HasCustomFieldsEntityInterface;
use App\Form\Type\CustomValueType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\PropertyAccess\PropertyAccess;

class CustomValueDataMapper extends PropertyPathMapper
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var string
     */
    private $customValueEntityClass;

    /**
     * @var CustomField[]|array
     */
    protected $customFieldStorage = [];

    /**
     * CustomValueDataMapper constructor.
     * @param EntityManager $entityManager
     * @param string $customValueEntityClass
     */
    public function __construct(
        EntityManager $entityManager,
        string $customValueEntityClass
    )
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        parent::__construct($propertyAccessor);
        $this->entityManager = $entityManager;
        $this->customValueEntityClass = $customValueEntityClass;
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($data, $forms)
    {
        parent::mapDataToForms($data, $forms);
        if ($data instanceof HasCustomFieldsEntityInterface) {
            $forms = iterator_to_array($forms);
            $fieldNames = array_keys($forms);
            $customValues = $data->getCustomValues();
            $assocCustomValues = [];
            foreach ($customValues as $customValue) {
                if (null !== $customField = $customValue->getCustomField()) {
                    $assocCustomValues[$customField->getId()] = $customValue;
                }
            }
            foreach ($fieldNames as $fieldName) {
                if (preg_match('/' . CustomValueType::FIELD_PREFIX . '_(\d+)/', $fieldName, $matches)) {
                    $customFieldId = (int)$matches[1];
                    if (array_key_exists($customFieldId, $assocCustomValues)) {
                        $forms[$fieldName]->setData($assocCustomValues[$customFieldId]->getValue());
                    }
                }
            }
        }
    }

    /**
     * @param int $customFieldId
     * @return CustomField|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function getCustomFieldById(int $customFieldId): ?CustomField
    {
        if (!array_key_exists($customFieldId, $this->customFieldStorage)) {
            $this->customFieldStorage[$customFieldId] = $this->entityManager->find(CustomField::class, $customFieldId);
        }
        return $this->customFieldStorage[$customFieldId];
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$data)
    {
        parent::mapFormsToData($forms, $data);
        if ($data instanceof HasCustomFieldsEntityInterface) {
            $forms = iterator_to_array($forms);
            $fieldNames = array_keys($forms);
            $customValues = $data->getCustomValues();
            $assocCustomValues = [];
            foreach ($customValues as $customValue) {
                if (null !== $customField = $customValue->getCustomField()) {
                    $assocCustomValues[$customField->getId()] = $customValue;
                }
            }
            foreach ($fieldNames as $fieldName) {
                if (preg_match('/' . CustomValueType::FIELD_PREFIX . '_(\d+)/', $fieldName, $matches)) {
                    $customFieldId = (int)$matches[1];
                    $formFieldData = trim((string)$forms[$fieldName]->getData());
                    if (array_key_exists($customFieldId, $assocCustomValues)) {
                        $customValue = $assocCustomValues[$customFieldId];
                        $customValue->setValue($formFieldData);
                    } elseif ($formFieldData !== '' && null !== $customField = $this->getCustomFieldById($customFieldId)) {
                        $customValue = new $this->customValueEntityClass();
                        $customValue->setCustomField($customField);
                        $customValue->setValue($formFieldData);
                        $data->addCustomValue($customValue);
                        $this->entityManager->persist($customValue);
                    }
                }
            }
        }
    }
}
