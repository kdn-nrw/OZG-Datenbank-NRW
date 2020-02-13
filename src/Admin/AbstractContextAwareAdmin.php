<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-02-11
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
abstract class AbstractContextAwareAdmin extends AbstractAdmin
{

    public const APP_CONTEXT_BE = 'backend';
    public const APP_CONTEXT_FE = 'frontend';

    protected $customShowFields = ['serviceSystems', 'laboratories', 'services', 'solutions', 'serviceProviders',];

    protected $appContext = self::APP_CONTEXT_BE;

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
        return $this->appContext === self::APP_CONTEXT_FE;
    }

    /**
     * @return array
     */
    public function getCustomShowFields(): array
    {
        return $this->customShowFields;
    }

}
