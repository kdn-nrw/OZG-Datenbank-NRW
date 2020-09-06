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

namespace App\Twig\Extension;

use App\Entity\Base\BaseEntityInterface;
use App\Model\EntityReferenceMap;
use App\Model\EntityReferenceProperty;
use App\Service\EntityReferenceMapper;
use Sonata\AdminBundle\Admin\AdminInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntityReferencesExtension extends AbstractExtension
{

    /**
     * @var EntityReferenceMapper
     */
    protected $entityReferenceMapper;

    /**
     * @param EntityReferenceMapper $entityReferenceMapper
     */
    public function __construct(EntityReferenceMapper $entityReferenceMapper)
    {
        $this->entityReferenceMapper = $entityReferenceMapper;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_entity_reference_fields', [$this, 'getEntityReferenceMap']),
            new TwigFunction('app_entity_reference_data', [$this, 'getEntityReferenceData']),
        ];
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param BaseEntityInterface|mixed $object
     * @param EntityReferenceProperty $reference
     * @return mixed|array
     */
    public function getEntityReferenceData($object, EntityReferenceProperty $reference)
    {
        return $this->entityReferenceMapper->getEntityReferenceData($object, $reference);
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param BaseEntityInterface|mixed $object
     * @param AdminInterface $admin
     * @param string $action
     * @return EntityReferenceMap
     */
    public function getEntityReferenceMap($object, AdminInterface $admin, string $action): EntityReferenceMap
    {
        $entityReferenceMap = $this->entityReferenceMapper->getEntityReferenceMetaData(get_class($object), $action);
        $entityReferenceMap->initObjectActions($object, $action);
        $referenceList = $entityReferenceMap->getPropertyReferences();
        $admin->setSubject($object);
        $show = $admin->getShow();
        if (null !== $show) {
            $elements = $show->getElements();
            $elementKeys = array_keys($elements);
            foreach ($elementKeys as $key) {
                if (array_key_exists($key, $referenceList)) {
                    $entityReferenceProperty = $referenceList[$key];
                    $fieldDescription = $show->get($key);
                    $entityReferenceProperty->setFieldDescription($fieldDescription);
                }
            }
        }
        return $entityReferenceMap;
    }
}
