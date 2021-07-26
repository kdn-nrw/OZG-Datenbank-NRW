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

use App\Entity\AddressTrait;
use App\Entity\Base\ContactPropertiesTrait;
use App\Entity\StateGroup\ServiceProvider;
use App\Import\DataParser;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ServiceAccount
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_service_account")
 */
class ServiceAccount extends AbstractOnboardingEntity
{
    public const DEFAULT_PAYMENT_PROVIDER = 'Girosolution';

    use AddressTrait;
    use ContactPropertiesTrait;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Onboarding\Contact", mappedBy="serviceAccount", cascade={"all"})
     */
    private $paymentUser;

    /**
     * @ORM\Column(type="string", name="mandator_email", length=255, nullable=true)
     * @var string|null
     */
    protected $mandatorEmail;

    /**
     * @var ServiceProvider|null
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\ServiceProvider", cascade={"persist"})
     * @ORM\JoinColumn(name="payment_operator_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $paymentOperator;

    /**
     * Has account mandator
     * @var int|null
     *
     * @ORM\Column(name="account_mandator_state", type="integer", nullable=true)
     * @deprecated
     */
    protected $accountMandatorState;

    /**
     * @ORM\Column(type="string", name="answer_url_1", length=1024, nullable=true)
     * @var string|null
     */
    protected $answerUrl1;

    /**
     * @ORM\Column(type="string", name="client_id", length=255, nullable=true)
     * @var string|null
     */
    protected $clientId;

    /**
     * @ORM\Column(type="string", name="client_password", length=255, nullable=true)
     * @var string|null
     */
    protected $clientPassword;

    /**
     * @ORM\Column(type="string", name="answer_url_2", length=1024, nullable=true)
     * @var string|null
     */
    protected $answerUrl2;

    /**
     * @ORM\Column(type="string", name="client_id_2", length=255, nullable=true)
     * @var string|null
     */
    protected $clientId2;

    /**
     * @ORM\Column(type="string", name="client_password_2", length=255, nullable=true)
     * @var string|null
     */
    protected $clientPassword2;

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
     * @return ServiceProvider|null
     */
    public function getPaymentOperator(): ?ServiceProvider
    {
        return $this->paymentOperator;
    }

    /**
     * @param ServiceProvider|null $paymentOperator
     */
    public function setPaymentOperator(?ServiceProvider $paymentOperator): void
    {
        $this->paymentOperator = $paymentOperator;
    }

    /**
     * @return string|null
     */
    public function getAnswerUrl1(): ?string
    {
        if (null === $this->answerUrl1 && null !== $this->commune) {
            $name = str_replace(' ', '-', strtolower(DataParser::cleanStringValue($this->getCommuneName())));
            $this->answerUrl1 = 'https://' . $name . '.kommunalportal.nrw/c/portal/login/servicekonto';
        }
        return $this->answerUrl1;
    }

    /**
     * @param string|null $answerUrl1
     */
    public function setAnswerUrl1(?string $answerUrl1): void
    {
        $this->answerUrl1 = $answerUrl1;
    }

    /**
     * @return string|null
     */
    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    /**
     * @param string|null $clientId
     */
    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string|null
     */
    public function getClientPassword(): ?string
    {
        return $this->clientPassword;
    }

    /**
     * @param string|null $clientPassword
     */
    public function setClientPassword(?string $clientPassword): void
    {
        $this->clientPassword = $clientPassword;
    }

    /**
     * @return string|null
     */
    public function getAnswerUrl2(): ?string
    {
        if (null === $this->answerUrl2 && null !== $this->commune) {
            $name = str_replace(' ', '-', strtolower(DataParser::cleanStringValue($this->getCommuneName())));
            $this->answerUrl2 = 'https://' . $name . '.test1.kommunalportal.nrw/c/portal/login/npa';
        }
        return $this->answerUrl2;
    }

    /**
     * @param string|null $answerUrl2
     */
    public function setAnswerUrl2(?string $answerUrl2): void
    {
        $this->answerUrl2 = $answerUrl2;
    }

    /**
     * @return string|null
     */
    public function getClientId2(): ?string
    {
        return $this->clientId2;
    }

    /**
     * @param string|null $clientId2
     */
    public function setClientId2(?string $clientId2): void
    {
        $this->clientId2 = $clientId2;
    }

    /**
     * @return string|null
     */
    public function getClientPassword2(): ?string
    {
        return $this->clientPassword2;
    }

    /**
     * @param string|null $clientPassword2
     */
    public function setClientPassword2(?string $clientPassword2): void
    {
        $this->clientPassword2 = $clientPassword2;
    }

    protected function getRequiredPropertiesForCompletion(): array
    {
        return [
            'paymentProviderAccountId', 'paymentUser', 'mandatorEmail',
            'street', 'zipCode', 'town',
        ];
    }
}
