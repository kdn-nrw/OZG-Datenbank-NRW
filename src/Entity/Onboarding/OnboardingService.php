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

namespace App\Entity\Onboarding;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\UrlTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class onboarding service
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_service")
 * @ORM\HasLifecycleCallbacks
 */
class OnboardingService extends BaseNamedEntity
{
    use UrlTrait;

    /**
     * @var EpaymentService[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Onboarding\EpaymentService", mappedBy="service", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     */
    private $epaymentServices;

    public function __construct()
    {
        $this->epaymentServices = new ArrayCollection();
    }

    /**
     * @param EpaymentService $epaymentService
     * @return self
     */
    public function addEpaymentService(EpaymentService $epaymentService): self
    {
        if (!$this->epaymentServices->contains($epaymentService)) {
            $this->epaymentServices->add($epaymentService);
            $epaymentService->setService($this);
        }

        return $this;
    }

    /**
     * @param EpaymentService $epaymentService
     * @return self
     */
    public function removeEpaymentService($epaymentService): self
    {
        if ($this->epaymentServices->contains($epaymentService)) {
            $this->epaymentServices->removeElement($epaymentService);
        }

        return $this;
    }

    /**
     * @return EpaymentService[]|Collection
     */
    public function getEpaymentServices()
    {
        return $this->epaymentServices;
    }

    /**
     * @param EpaymentService[]|Collection $epaymentServices
     */
    public function setEpaymentServices($epaymentServices): void
    {
        $this->epaymentServices = $epaymentServices;
    }
}
