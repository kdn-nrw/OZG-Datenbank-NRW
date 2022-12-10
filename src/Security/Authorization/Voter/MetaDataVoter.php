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

namespace App\Security\Authorization\Voter;


use App\Entity\MetaData\AbstractMetaItem;
use App\Entity\MetaData\MetaItem;
use App\Entity\MetaData\MetaItemProperty;
use App\Service\InjectAdminManagerTrait;
use App\Util\SnakeCaseConverter;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class MetaDataVoter
 */
class MetaDataVoter extends Voter
{
    use InjectAdminManagerTrait;

    /**
     * @var array
     */
    protected $metaCache = [];

    protected const BASE_ROLE_PREFIX = 'ROLE_APP\ADMIN\METADATA\METAITEMADMIN_';
    protected const VIEW = 'VIEW';
    protected const EDIT = 'EDIT';
    protected const DELETE = 'DELETE';
    protected const CREATE = 'CREATE';
    protected const LIST = 'LIST';

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            self::BASE_ROLE_PREFIX . self::VIEW,
            self::BASE_ROLE_PREFIX . self::DELETE,
            self::BASE_ROLE_PREFIX . self::EDIT,
            self::BASE_ROLE_PREFIX . self::CREATE,
        ])) {
            return false;
        }
        // only vote on MetaItem objects inside this voter
        if ($subject instanceof MetaItem || $subject instanceof MetaItemProperty) {
            return true;
        }

        return false;
    }

    protected function getEntityClassForKey(string $entityClassMetaKey)
    {
        if (!array_key_exists('entities', $this->metaCache)) {
            $this->metaCache['entities'] = [];
            $entityClasses = $this->adminManager->getManagedEntityClasses();
            foreach ($entityClasses as $entityClass) {
                $metaKey = SnakeCaseConverter::classNameToSnakeCase($entityClass);
                $this->metaCache['entities'][$metaKey] = $entityClass;
            }
        }
        return $this->metaCache['entities'][$entityClassMetaKey] ?? null;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }
        $metaItem = $subject instanceof MetaItemProperty ? $subject->getParent() : $subject;
        if ($metaItem->getMetaType() === AbstractMetaItem::META_TYPE_ENTITY) {
            $entityClass = $this->getEntityClassForKey($metaItem->getMetaKey());
            return !empty($entityClass) && $this->canEdit($entityClass);
        }
        return true;
    }

    private function canEdit(string $entityClass): bool
    {
        $admin = $this->adminManager->getAdminByEntityClass($entityClass);
        if (null !== $admin) {
            return $admin->isGranted('EDIT');
        }
        // if they can edit, they can view
        return false;
    }
}
