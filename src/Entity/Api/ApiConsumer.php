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

namespace App\Entity\Api;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\UrlTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class API consumer
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_api_consumer")
 */
class ApiConsumer extends BaseNamedEntity
{
    use UrlTrait;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Proxy url
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $proxy;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $consumerKey;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getProxy(): ?string
    {
        return $this->proxy;
    }

    /**
     * @param string|null $proxy
     */
    public function setProxy(?string $proxy): void
    {
        $this->proxy = $proxy;
    }

    /**
     * @return string|null
     */
    public function getConsumerKey(): ?string
    {
        return $this->consumerKey;
    }

    /**
     * @param string|null $consumerKey
     */
    public function setConsumerKey(?string $consumerKey): void
    {
        $this->consumerKey = $consumerKey;
    }

}
