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

namespace App\Service\Mailer;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\DependencyInjection\InjectionTraits\InjectSecurityTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\ColorCodedEntityInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\PersonInterface;
use App\Entity\Base\SluggableInterface;
use App\Entity\Configuration\EmailTemplate;
use App\Model\EmailTemplate\AbstractTemplateModel;
use App\Model\EmailTemplate\OnboardingCommuneInfoUpdateModel;
use App\Model\EmailTemplate\OnboardingEpaymentUpdateModel;
use App\Model\EmailTemplate\OnboardingFormSolutionUpdateModel;
use App\Model\EmailTemplate\OnboardingServiceAccountUpdateModel;
use App\Service\InjectAdminManagerTrait;
use App\Translator\TranslatorAwareTrait;
use App\Util\SnakeCaseConverter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Storage\FileSystemStorage;

class EmailTemplateManager
{
    use InjectAdminManagerTrait;
    use InjectManagerRegistryTrait;
    use InjectSecurityTrait;
    use TranslatorAwareTrait;
    use InjectMarkerServiceTrait;

    /**
     * @var BaseMailer
     */
    private $mailer;

    /**
     * @var FileSystemStorage
     */
    private $attachmentStorage;

    /**
     * MailingSender constructor.
     * @param BaseMailer $mailer
     * @param FileSystemStorage $attachmentStorage
     * @param TranslatorInterface $translator
     */
    public function __construct(BaseMailer $mailer, FileSystemStorage $attachmentStorage, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->attachmentStorage = $attachmentStorage;
        $this->setTranslator($translator);
    }

    public function createTemplates(): void
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(EmailTemplate::class);
        $templates = $repository->findAll();
        $templatesKeys = array_keys($this->getEmailTemplates());
        $instanceRegistry = [];
        foreach ($templates as $template) {
            /** @var EmailTemplate $template */
            $instanceRegistry[$template->getTemplateKey()] = 1;
        }
        foreach ($templatesKeys as $key) {
            if (!array_key_exists($key, $instanceRegistry)
                && null !== $modelInstance = $this->initializeEmailTemplateModelInstance($key)) {
                $entity = $modelInstance->newEmailTemplateInstance();
                $em->persist($entity);
            }
        }
        $em->flush();
    }

    /**
     * After entity create
     *
     * @param BaseEntityInterface $object
     */
    public function sendNotificationsAfterCreate(BaseEntityInterface $object): void
    {
        $key = self::getEntityTemplateKey(get_class($object), 'create');
        $model = $this->getEmailTemplateModel($key);
        if (null !== $model) {
            $model->setVariable('entity', $object);
            $this->sendMarkerEmail($model);
        }
    }

    /**
     * After entity update
     *
     * @param BaseEntityInterface $object
     */
    public function sendNotificationsAfterUpdate(BaseEntityInterface $object): void
    {
        $key = self::getEntityTemplateKey(get_class($object), 'update');
        $model = $this->getEmailTemplateModel($key);
        if (null !== $model) {
            $model->setVariable('entity', $object);
            $this->sendMarkerEmail($model);
        }
    }

    protected function sendMarkerEmail(AbstractTemplateModel $model, $sendHtmlPart = false): bool
    {
        $emailTemplate = $model->getEmailTemplate();
        if ($emailTemplate->isHidden()) {
            return false;
        }
        $mailer = $this->mailer;
        $email = $mailer->createMessage();
        $recipient = $model->getRecipient();
        if (null === $recipient) {
            $recipient = $mailer->createAddress($emailTemplate->getDefaultRecipient(), null);
        }
        $email->to($recipient);
        if ($senderEmail = $emailTemplate->getSenderEmail()) {
            $email->from($mailer->createAddress($senderEmail, $emailTemplate->getSenderName()));
        }
        $ccEmailArray = $emailTemplate->getCcAddressList();
        if (!empty($ccEmailArray)) {
            foreach ($ccEmailArray as $ccEmail) {
                $email->addCc($mailer->createAddress($ccEmail, null));
            }
        }
        $markers = $this->markerService->getModelMarkers($model);
        $search = array_keys($markers);
        $user = $this->security->getUser();
        $model->setUser($user);
        $subject = str_replace($search, $markers, $emailTemplate->getSubject());
        $lines = explode(PHP_EOL, str_replace($search, $markers, $emailTemplate->getBody()));
        $emptyLineCount = 0;
        // Remove more than two consecutive empty lines in plain email text
        $messagePlain = '';
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if (empty($trimmedLine)) {
                ++$emptyLineCount;
                if ($emptyLineCount < 2) {
                    $messagePlain .= $trimmedLine . PHP_EOL;
                }
            } else {
                $emptyLineCount = 0;
                $messagePlain .= $trimmedLine . PHP_EOL;
            }
        }
        $email->subject($subject)
            ->text($messagePlain);
        if ($sendHtmlPart) {
            $messageHtml = $messagePlain;
            $messagePlain = trim(strip_tags($messagePlain));
            if (strpos($messageHtml, '<body') === false) {
                $messageHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml" lang="de">
                     <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                      <title>' . $subject . '</title>
                      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                    </head>
                    <body style="margin: 0; padding: 0;">' . $messageHtml . '</body>
                    </html>';
            }
            $email->html($messagePlain);
            $email->html($messageHtml);
        } else {
            $email->html($messagePlain);
        }
        return $mailer->sendMessage($email);
    }

    /**
     * Returns the internal key for the given entity class
     *
     * @param string $entityClass
     * @param string $processType
     * @return string
     */
    public static function getEntityTemplateKey(string $entityClass, string $processType = ''): string
    {
        $entityKey = trim(str_replace(['\\', 'App_Entity'], ['_', ''], $entityClass), ' _');
        $entityKey = strtoupper(SnakeCaseConverter::camelCaseToSnakeCase($entityKey));
        return strtolower($entityKey . ($processType ? '_' . $processType : ''));
    }

    /**
     * @param string|EmailTemplate $keyOrEntity
     * @return AbstractTemplateModel|null
     */
    public function getEmailTemplateModel($keyOrEntity): ?AbstractTemplateModel
    {
        if ($keyOrEntity instanceof EmailTemplate) {
            $templateKey = $keyOrEntity->getTemplateKey();
        } else {
            $templateKey = (string)$keyOrEntity;
        }
        $model = $this->initializeEmailTemplateModelInstance($templateKey);
        if (null !== $model) {
            if ($keyOrEntity instanceof EmailTemplate) {
                $model->setEmailTemplate($keyOrEntity);
            } elseif (null !== $emailTemplate = $this->getEmailTemplateByKey($templateKey)) {
                $model->setEmailTemplate($emailTemplate);
            } else {
                $model->setEmailTemplate($model->newEmailTemplateInstance());
            }
            $model->setUser($this->security->getUser());
            return $model;
        }
        return null;
    }

    public function getEmailTemplateByKey(string $key): ?EmailTemplate
    {
        $repository = $this->getEntityManager()->getRepository(EmailTemplate::class);
        return $repository->findOneBy(['templateKey' => $key]);
    }

    /**
     * Returns a new email template model instance for the given key.
     * The instance does not contain variables yet, these must be set by the service!
     *
     * @param string $templateKey
     * @return AbstractTemplateModel|null
     */
    public function initializeEmailTemplateModelInstance(string $templateKey): ?AbstractTemplateModel
    {
        $templates = $this->getEmailTemplates();
        if (array_key_exists($templateKey, $templates)) {
            return new $templates[$templateKey]();
        }
        return null;
    }

    public function getEmailTemplates(): array
    {
        return [
            OnboardingServiceAccountUpdateModel::TEMPLATE_KEY => OnboardingServiceAccountUpdateModel::class,
            OnboardingCommuneInfoUpdateModel::TEMPLATE_KEY => OnboardingCommuneInfoUpdateModel::class,
            OnboardingEpaymentUpdateModel::TEMPLATE_KEY => OnboardingEpaymentUpdateModel::class,
            OnboardingFormSolutionUpdateModel::TEMPLATE_KEY => OnboardingFormSolutionUpdateModel::class,
        ];
    }

    /**
     * Returns the email template choices (used in admin form)
     * @return array
     */
    public function getEmailTemplatesChoices(): array
    {
        $choices = [];
        $templatesKeys = array_keys($this->getEmailTemplates());
        foreach ($templatesKeys as $templateKey) {
            $modelInstance = $this->initializeEmailTemplateModelInstance($templateKey);
            if (null !== $modelInstance) {
                $choices[$templateKey] = $modelInstance->getTemplateLabel() ?: $templateKey;
            }
        }
        return $choices;
    }

    /**
     * @param AbstractTemplateModel $model
     * @param bool $useTestData
     * @return array The model markers
     */
    public function getModelMarkers(AbstractTemplateModel $model, $useTestData = false): array
    {
        if ($useTestData) {
            $variableDefinitions = $model->getVariableDefinitions();
            foreach ($variableDefinitions as $variableDefinition) {
                $options = $variableDefinition->getOptions();
                $type = $variableDefinition->getType();
                $value = $options['default'] ?? null;
                if (null === $value) {
                    switch ($type) {
                        case 'string':
                            $value = 'Lorem ipsum';
                            break;
                        case 'entity':
                            $repository = $this->getEntityManager()->getRepository($options['class']);
                            $queryBuilder = $repository->createQueryBuilder('e');
                            $queryBuilder->orderBy('e.id', 'desc');
                            $queryBuilder->setMaxResults(1);
                            $query = $queryBuilder->getQuery();
                            $entity = null;
                            try {
                                $storedEntity = $query->getOneOrNullResult();
                                if (null !== $storedEntity) {
                                    $entity = clone $storedEntity;
                                }
                            } catch (NonUniqueResultException $e) {
                                $entity = null;
                            }
                            if (null === $entity) {
                                $entity = new $options['class']();
                            }
                            $properties = [];
                            $properties['email'] = 'example@example.com';
                            $properties['hidden'] = 'false';
                            $properties['description'] = 'Lorem ipsum sit dolor amet';
                            if ($entity instanceof NamedEntityInterface) {
                                $properties['name'] = 'Lorem ipsum';
                            }
                            if ($entity instanceof PersonInterface) {
                                $properties['firstName'] = 'Marie';
                                $properties['lastName'] = 'Musterfrau';
                                $properties['gender'] = PersonInterface::GENDER_FEMALE;
                            }
                            if ($entity instanceof ColorCodedEntityInterface) {
                                $properties['color'] = '#0099ff';
                                $properties['cssClass'] = 'bg-primary';
                            }
                            if ($entity instanceof SluggableInterface) {
                                $properties['slug'] = 'lorem-ipsum-' . random_int(1, 1000);
                            }
                            $propertyAccessor = PropertyAccess::createPropertyAccessor();
                            foreach ($properties as $propertyName => $propertyValue) {
                                if ($propertyAccessor->isWritable($entity, $propertyName)) {
                                    $propertyAccessor->setValue($entity, $propertyName, $propertyValue);
                                }
                            }
                            $value = $entity;
                            break;
                    }
                }
                $model->setVariable($variableDefinition->getName(), $value);
            }
        }
        return $this->markerService->getModelMarkers($model);
    }
}