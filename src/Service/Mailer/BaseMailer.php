<?php

namespace App\Service\Mailer;

use App\Entity\Contact;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class BaseMailer
{
    /**
     * @var ExceptionInterface|null
     */
    private $lastException;

    /**
     * @var bool
     */
    protected $isDebug = false;

    /**
     * @var string
     */
    private $debugEmail;

    /**
     * @var MailerInterface
     */
    private $mailerInterface;
    /**
     * @var string
     */
    private $fromEmail;
    /**
     * @var string
     */
    private $fromName;
    /**
     * @var string
     */
    private $adminEmail;

    /**
     * BaseMailer constructor.
     * @param MailerInterface $mailerInterface
     * @param string $fromEmail
     * @param string $fromName
     * @param string $adminEmail
     * @param bool $isDebug
     * @param string $debugEmail
     */
    public function __construct(
        MailerInterface $mailerInterface,
        string $fromEmail,
        string $fromName,
        string $adminEmail,
        bool $isDebug,
        string $debugEmail
    )
    {
        $this->mailerInterface = $mailerInterface;
        $this->isDebug = $isDebug;
        $this->debugEmail = $debugEmail;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @param Address|string|null $fromAddresses
     *
     * @return Email
     */
    public function createMessage($fromAddresses = null): Email
    {
        $email = new Email();
        if ($fromAddresses) {
            $email->from($fromAddresses);
        } else {
            $email->from($this->createAddress($this->fromEmail, $this->fromName));
        }
        return $email;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @return Address
     */
    public function createAddress(string $email, ?string $name): Address
    {
        if ($name) {
            $address = new Address($email, $name);
        } else {
            $address = new Address($email);
        }
        return $address;
    }

    /**
     * Send the given message
     *
     * @param Email $message
     * @return bool
     */
    public function sendMessage(Email $message): bool
    {
        /** @var Mailer $mailer */
        $mailer = $this->mailerInterface;
        /** @var SentMessage $sentEmail */
        try {
            if ($this->isDebug) {
                $debugTable = $this->getDebugTable($message);
                $debugPlain = $this->formatDebugTablePlain($debugTable);
                $message->to($this->debugEmail);
                $headers = $message->getHeaders();
                if ($headers->has('Cc')) {
                    $headers->remove('Cc');
                }
                if ($headers->has('Bcc')) {
                    $headers->remove('Bcc');
                }
                $message->text($message->getTextBody() . $debugPlain);
            }
            $mailer->send($message);
            $this->lastException = null;
        } catch (TransportExceptionInterface $e) {
            $this->lastException = $e;
            return false;
        }
        return true;
    }

    /**
     * @return ExceptionInterface|null
     */
    public function getLastException(): ?ExceptionInterface
    {
        return $this->lastException;
    }

    /**
     * @param Email $message
     * @return array
     */
    private function getDebugTable(Email $message): array
    {
        return [
            'To' => $this->formatAndMergeRecipients($message->getTo()),
            'Cc' => $this->formatAndMergeRecipients($message->getCc()),
            'Bcc' => $this->formatAndMergeRecipients($message->getBcc()),
        ];
    }

    /**
     * @param $recipients
     * @param string $glue
     * @return string
     */
    private function formatAndMergeRecipients($recipients, $glue = ', '): string
    {
        $recipientArray = [];
        if (!empty($recipients)) {
            foreach ($recipients as $key => $value) {
                if ($value instanceof Address) {
                    $recipientArray[] = $value->toString();
                } elseif (!is_numeric($key)) {
                    $recipientArray[] = sprintf('"%s" <%s>', $value, $key);
                } else {
                    $recipientArray[] = $value;
                }
            }
        }

        return implode($glue, $recipientArray);
    }

    /**
     * @param array $debugTable
     * @return string
     */
    private function formatDebugTablePlain($debugTable): string
    {
        $debugOutput = PHP_EOL . PHP_EOL . PHP_EOL;
        $debugOutput .= str_pad('', 60, '-', STR_PAD_LEFT) . PHP_EOL;
        $debugOutput .= 'DEBUG-INFORMATION:' . PHP_EOL;
        foreach ($debugTable as $key => $value) {
            $debugOutput .= mb_strtoupper($key) . ': ' . $value . PHP_EOL;
        }

        return $debugOutput;
    }

    public function getGreeting(Contact $contact): string
    {
        $gender = $contact->getGender();
        $greeting = 'Sehr geehrte Damen und Herren';
        $lastName = $contact->getLastName();
        if ($lastName) {
            $appendFirstName = false;
            $appendTitleAndName = true;
            switch ($gender) {
                case Contact::GENDER_MALE:
                    $greeting = 'Sehr geehrter Herr';
                    break;
                case Contact::GENDER_FEMALE:
                    $greeting = 'Sehr geehrte Frau';
                    break;
                case Contact::GENDER_OTHER:
                    $greeting = 'Guten Tag';
                    $appendFirstName = true;
                    break;
                default:
                    $appendTitleAndName = false;
                    break;
            }
            if ($appendTitleAndName) {
                $title = $contact->getTitle();
                if ($title) {
                    $greeting .= ' ' . $title;
                }
                if ($appendFirstName && $firstName = $contact->getFirstName()) {
                    $greeting .= ' ' . $firstName;
                }
                $greeting .= ' ' . $lastName;
            }
        }
        return $greeting . ',';
    }

}