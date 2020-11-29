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

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;

trait InjectCacheTrait
{

    /**
     * @var CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @param CacheItemPoolInterface $cache
     * @required
     */
    public function injectCache(CacheItemPoolInterface $cache): void
    {
        $this->cache = $cache;
    }

}