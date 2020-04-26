<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;

/**
 * Class AbstractContextAwareAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-02-11
 */
abstract class AbstractContextAwareAdmin extends AbstractAdmin implements ContextAwareAdminInterface
{
    protected $customShowFields = ['serviceSystems', 'laboratories', 'services', 'publishedSolutions', 'solutions', 'serviceProviders',];

    protected $appContext = ContextAwareAdminInterface::APP_CONTEXT_BE;

    /**
     * @param string $appContext
     */
    public function setAppContext(string $appContext): void
    {
        $this->appContext = $appContext;
    }

    /**
     * @return string
     */
    public function getAppContext(): string
    {
        return $this->appContext;
    }

    protected function isFrontend(): bool
    {
        return $this->appContext === ContextAwareAdminInterface::APP_CONTEXT_FE;
    }

    /**
     * @return array
     */
    public function getCustomShowFields(): array
    {
        return $this->customShowFields;
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $fields = parent::getExportFields();
        $excludeFields = $this->getExportExcludeFields();
        if (!empty($excludeFields)) {
            $fields = array_diff($fields, $excludeFields);
        }
        return $fields;
    }

    /**
     * @return array
     */
    protected function getExportExcludeFields(): array
    {
        return [];
    }

    public function getExportFormats()
    {
        return ['xlsx'];
    }
}
