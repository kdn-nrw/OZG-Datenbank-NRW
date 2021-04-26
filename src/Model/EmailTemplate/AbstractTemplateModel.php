<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\EmailTemplate;

use App\Entity\Configuration\EmailTemplate;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractTemplateModel
{
    /**
     * @var string|null
     */
    protected $templateKey;

    /**
     * @var string|null
     */
    protected $templateLabel;

    /**
     * @var EmailTemplate
     */
    protected $emailTemplate;

    /**
     * @var UserInterface|null
     */
    protected $user;

    /**
     * @var array|EmailVariableModel[]
     */
    protected $variableDefinitions;

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var Address|null
     */
    protected $recipient;

    /**
     * @return string|null
     */
    public function getTemplateKey(): ?string
    {
        return $this->templateKey;
    }

    /**
     * Returns the variables expected by this model
     * @return array|EmailVariableModel[]
     */
    public function getVariableDefinitions(): array
    {
        if (null === $this->variableDefinitions) {
            $this->initializeVariableDefinitions();
        }
        return $this->variableDefinitions;
    }

    /**
     * Initializes the variables definitions for this model
     */
    abstract public function initializeVariableDefinitions(): void;

    /**
     * @return EmailTemplate
     */
    public function getEmailTemplate(): EmailTemplate
    {
        return $this->emailTemplate;
    }

    /**
     * @param EmailTemplate $emailTemplate
     */
    public function setEmailTemplate(EmailTemplate $emailTemplate): void
    {
        $this->emailTemplate = $emailTemplate;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     */
    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setVariable(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @return string|null
     */
    public function getTemplateLabel(): ?string
    {
        return $this->templateLabel;
    }

    /**
     * Returns a new EmailTemplate with default values for the current model
     * @return EmailTemplate
     */
    public function newEmailTemplateInstance(): EmailTemplate
    {
        $template = new EmailTemplate($this->getTemplateKey());
        $template->setHidden(true);
        return $template;
    }

    /**
     * @return Address|null
     */
    public function getRecipient(): ?Address
    {
        return $this->recipient;
    }

    /**
     * @param Address|null $recipient
     */
    public function setRecipient(?Address $recipient): void
    {
        $this->recipient = $recipient;
    }

}