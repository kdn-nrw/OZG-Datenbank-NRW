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

use Doctrine\ORM\Mapping as ORM;


/**
 * Class ePayBL
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_epayment")
 */
class Epayment extends AbstractOnboardingEntity
{
    public const DEFAULT_PAYMENT_PROVIDER = 'Girosolution';
    /**
     * @ORM\Column(type="string", name="payment_provider_account_id", length=255, nullable=true)
     * @var string|null
     */
    protected $paymentProviderAccountId;

    /**
     * Payment provider
     *
     * @var string|null
     *
     * @ORM\Column(type="string", name="payment_provider", length=255, nullable=true)
     */
    protected $paymentProvider = self::DEFAULT_PAYMENT_PROVIDER;

    /**
     * @var Contact|null
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\Contact", mappedBy="epayment", cascade={"all"})
     */
    private $paymentUser;

    /**
     * @ORM\Column(type="string", name="mandator_email", length=255, nullable=true)
     * @var string|null
     */
    protected $mandatorEmail;

    /**
     * @ORM\Column(type="string", name="test_ip_address", length=255, nullable=true)
     * @var string|null
     */
    protected $testIpAddress;

    /**
     * @return string|null
     */
    public function getPaymentProviderAccountId(): ?string
    {
        return $this->paymentProviderAccountId;
    }

    /**
     * @param string|null $paymentProviderAccountId
     */
    public function setPaymentProviderAccountId(?string $paymentProviderAccountId): void
    {
        $this->paymentProviderAccountId = $paymentProviderAccountId;
    }

    /**
     * @return string|null
     */
    public function getPaymentProvider(): ?string
    {
        return $this->paymentProvider ?? self::DEFAULT_PAYMENT_PROVIDER;
    }

    /**
     * @param string|null $paymentProvider
     */
    public function setPaymentProvider(?string $paymentProvider): void
    {
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * @return Contact
     */
    public function getPaymentUser(): Contact
    {
        if (null === $this->paymentUser) {
            $this->paymentUser = new Contact($this, Contact::CONTACT_TYPE_EPAYMENT_USER);
        }
        return $this->paymentUser;
    }

    /**
     * @param Contact|null $paymentUser
     */
    public function setPaymentUser(?Contact $paymentUser): void
    {
        $this->paymentUser = $paymentUser;
    }

    /**
     * @return string|null
     */
    public function getMandatorEmail(): ?string
    {
        return $this->mandatorEmail;
    }

    /**
     * @param string|null $mandatorEmail
     */
    public function setMandatorEmail(?string $mandatorEmail): void
    {
        $this->mandatorEmail = $mandatorEmail;
    }

    /**
     * @return string|null
     */
    public function getTestIpAddress(): ?string
    {
        return $this->testIpAddress;
    }

    /**
     * @param string|null $testIpAddress
     */
    public function setTestIpAddress(?string $testIpAddress): void
    {
        $this->testIpAddress = $testIpAddress;
    }

    protected function getRequiredPropertiesForCompletion(): array
    {
        return ['paymentProviderAccountId', 'paymentUser', 'mandatorEmail', 'testIpAddress'];
    }
}
