<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\CmsContactImport;
use App\Service\Mailer\MailingSender;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CronJob("*\/5 * * * *")
 * Will be executed every 5 minutes
 */
class MailingSendCommand extends Command
{
    protected static $defaultName = 'app:mailing:send';
    /**
     * @var MailingSender
     */
    private $mailingSender;

    /**
     * @param MailingSender $mailingSender
     */
    public function __construct(MailingSender $mailingSender)
    {
        parent::__construct();
        $this->mailingSender = $mailingSender;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Send emails for active mailings')
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_REQUIRED,
                'Limit number of emails sent',
                50
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $limit = max(1, min(500, (int) $input->getOption('limit')));
        $this->mailingSender->run($limit);
    }
}