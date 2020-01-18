<?php

namespace App\Service\Mailer;

use App\Entity\Mailing;
use App\Entity\MailingContact;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\VarDumper\VarDumper;

class MailingSender
{
    /**
     * @var BaseMailer
     */
    private $mailer;
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * MailingSender constructor.
     * @param ManagerRegistry $registry
     * @param BaseMailer $mailer
     */
    public function __construct(ManagerRegistry $registry, BaseMailer $mailer)
    {
        $this->registry = $registry;
        $this->mailer = $mailer;
    }

    /**
     * Send mailing emails
     *
     * @param int $limit
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run($limit = 50)
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManager();
        $qb = $em->createQueryBuilder();

        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('UTC'));
        $qb->select(['m'])
            ->from(Mailing::class, 'm')
            ->where('m.status IN (:statusList)')
            ->andWhere('m.hidden = 0')
            ->andWhere('m.sendStartAt IS NULL OR m.sendStartAt <= :now')
            ->setParameter('statusList', [Mailing::STATUS_PREPARED, Mailing::STATUS_ACTIVE]);
        $qb->setParameter('now', $now->format('Y-m-d H:i:s'));
        $qb->orderBy('m.createdAt', 'ASC');
        $query = $qb->getQuery();
        $results = $query->getResult();
        $activeContactStatus = [
            Mailing::STATUS_NEW,
            Mailing::STATUS_PREPARED,
            Mailing::STATUS_ACTIVE,
        ];
        $sentCount = 0;
        foreach ($results as $mailing) {
            /** @var Mailing $mailing */
            if ($mailing->getStatus() === Mailing::STATUS_PREPARED) {
                $mailing->setStatus(Mailing::STATUS_ACTIVE);
            }
            if (null === $mailing->getSendStartAt()) {
                $now = new \DateTime();
                $now->setTimezone(new \DateTimeZone('UTC'));
                $mailing->setSendStartAt($now);
            }
            $mailingContacts = $mailing->getMailingContacts();
            $setStatusFinished = true;
            foreach ($mailingContacts as $mc) {
                if (!$mc->isHidden() && in_array($mc->getSendStatus(), $activeContactStatus)) {
                    if (!$mailing->contactIsBlacklisted($mc->getContact())) {
                        $isSent = $this->sendEmailForMailingContact($mailing, $mc);
                        if ($isSent) {
                            $mc->finish();
                        } else {
                            //$exception = $this->mailer->getLastException();
                            $mc->incrementSendAttempts();
                        }
                        ++$sentCount;
                    }
                }
                if ($sentCount >= $limit) {
                    $setStatusFinished = false;
                    break;
                }
            }
            if ($setStatusFinished) {
                $mailing->finish();
            } else {
                $mailing->setSendEndAt(null);
            }
            $mailing->updateSentCount();
            $em->flush();
        }
    }

    private function sendEmailForMailingContact(Mailing $mailing, MailingContact $mailingContact)
    {
        $mailer = $this->mailer;
        $email = $mailer->createMessage();
        $contact = $mailingContact->getContact();

        $email->to($mailer->createAddress($contact->getEmail(), $contact->getFullName()));
        $plainText = $mailing->getTextPlain();
        if ($mailing->getGreetingType() === Mailing::GREETING_TYPE_PREPEND) {
            $greeting = $mailer->getGreeting($contact);
            $plainText = $greeting . "\n\n" . $plainText;
        }
        if ($senderEmail = $mailing->getSenderEmail()) {
            $email->from($mailer->createAddress($senderEmail, $mailing->getSenderName()));
        }
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        $email->subject($mailing->getSubject())
            ->text($plainText);
        //$email->html('<p>See Twig integration for better HTML integration!</p>');

        return $mailer->sendMessage($email);
    }

}