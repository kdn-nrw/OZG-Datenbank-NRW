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

namespace App\EventSubscriber;

use App\Entity\Base\BaseEntity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractEvent;

/*
 * Class SearchIndexEntityEvent
 */

class SearchIndexEntityEvent extends ContractEvent
{
    /**
     * @var BaseEntity
     */
    protected $object;

    /**
     * @var AbstractAdmin
     */
    protected $admin;

    /**
     * @var array
     */
    private $fullTextSearchWords;

    /**
     * SearchIndexEntityEvent constructor.
     * @param AdminInterface $admin
     * @param BaseEntity $object
     * @param array $fullTextSearchWords
     */
    public function __construct(AdminInterface $admin, BaseEntity $object, array $fullTextSearchWords)
    {
        $this->object = $object;
        $this->fullTextSearchWords = $fullTextSearchWords;
        $this->admin = $admin;
    }

    /**
     * Accessor to the object being manipulated.
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getFullTextSearchWords(): array
    {
        return $this->fullTextSearchWords;
    }

    /**
     * @param array $fullTextSearchWords
     */
    public function setFullTextSearchWords(array $fullTextSearchWords): void
    {
        $this->fullTextSearchWords = $fullTextSearchWords;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

}
