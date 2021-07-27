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

namespace App\Form\Filter;

use Sonata\AdminBundle\Filter\Persister\FilterPersisterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This filter persister is storing filters in session.
 * Adds groups for
 */
final class GroupedSessionFilterPersister implements FilterPersisterInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @required
     * @param SessionInterface $session
     */
    public function injectSessionInterface(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function get($adminCode): array
    {
        $values = $this->session->get($this->buildStorageKey($adminCode), []);
        $groupValues = $this->session->get($this->buildGroupStorageKey($adminCode), []);
        if (!empty($groupValues)) {
            $values = array_merge($groupValues, $values);
        }
        return $values;
    }

    public function set($adminCode, array $filters): void
    {
        $groupFilters = $this->session->get($this->buildGroupStorageKey($adminCode), []);
        if (!empty($filters)) {
            $groupFilters = array_merge($groupFilters, $filters);
        }
        $this->session->set($this->buildStorageKey($adminCode), $filters);
        $this->session->set($this->buildGroupStorageKey($adminCode), $groupFilters);
    }

    public function reset($adminCode): void
    {
        $this->session->remove($this->buildStorageKey($adminCode));
        $this->session->remove($this->buildGroupStorageKey($adminCode));
    }

    /**
     * Build the session key, under which the filter should be stored for given admin code.
     */
    private function buildStorageKey(string $adminCode): string
    {
        return sprintf('%s.filter.parameters', $adminCode);
    }

    /**
     * Build the session key, under which the filter should be stored for given admin code.
     */
    private function buildGroupStorageKey(string $adminCode): string
    {
        $parts = explode('\\', strtolower($adminCode));
        array_pop($parts);
        return sprintf('%s.filter.parameters', implode('.', $parts));
    }
}
