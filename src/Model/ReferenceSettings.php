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

namespace App\Model;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class ReferenceSettings
{
    /**
     * @var AbstractAdmin
     */
    protected $admin;

    /**
     * @var bool
     */
    protected $isBackendMode = false;

    /**
     * @var array
     */
    protected $show = [
        'enabled' => true,
        'route' => 'show',
        'enableSlug' => false,
    ];

    /**
     * @var array
     */
    protected $edit = [
        'enabled' => false,
        'route' => 'edit',
    ];

    /**
     * @var string|null
     */
    protected $labelPrefix;

    /**
     * @var string
     */
    protected $listTitle = 'app.common.fields.references';

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @return AbstractAdmin|null
     */
    public function getAdmin(): ?AbstractAdmin
    {
        return $this->admin;
    }

    /**
     * @param AbstractAdmin $admin
     */
    public function setAdmin(AbstractAdmin $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @return bool
     */
    public function isBackendMode(): bool
    {
        return $this->isBackendMode;
    }

    /**
     * @param bool $isBackendMode
     */
    public function setIsBackendMode(bool $isBackendMode): void
    {
        $this->isBackendMode = $isBackendMode;
    }

    /**
     * @return array
     */
    public function getShow(): array
    {
        return $this->show;
    }

    /**
     * @param bool $enabled Toggle enable mode for show link
     * @param string $route Set route for show action
     * @param bool $enableSlug Toggle slug feature
     */
    public function setShow(bool $enabled, string $route = 'show', bool $enableSlug = false): void
    {
        $this->show = [
            'enabled' => $enabled,
            'route' => $route,
            'enableSlug' => $enableSlug,
        ];
    }

    /**
     * @return array
     */
    public function getEdit(): array
    {
        return $this->edit;
    }

    /**
     * @param bool $enabled
     * @param string $route
     */
    public function setEdit(bool $enabled, string $route = 'edit'): void
    {
        $this->edit = [
            'enabled' => $enabled,
            'route' => $route,
        ];
    }

    /**
     * @return string|null
     */
    public function getLabelPrefix(): ?string
    {
        return $this->labelPrefix;
    }

    /**
     * @param string|null $labelPrefix
     */
    public function setLabelPrefix(?string $labelPrefix): void
    {
        $this->labelPrefix = $labelPrefix;
    }

    /**
     * @return string
     */
    public function getListTitle(): string
    {
        return $this->listTitle;
    }

    /**
     * @param string $listTitle
     */
    public function setListTitle(string $listTitle): void
    {
        $this->listTitle = $listTitle;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass(string $entityClass): void
    {
        $this->entityClass = $entityClass;
    }

}
