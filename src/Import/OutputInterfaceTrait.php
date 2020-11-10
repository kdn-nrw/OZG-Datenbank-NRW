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

namespace App\Import;

use Symfony\Component\Console\Output\OutputInterface;

trait OutputInterfaceTrait
{

    /**
     * @var OutputInterface|null
     */
    protected $output;

    /**
     * @param OutputInterface|null $output
     */
    public function setOutput(?OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Create debug message with given verbosity
     *
     * @param string $message The message
     * @param int $verbosity The verbosity controls which messages are displayed
     */
    protected function debug(string $message, int $verbosity = OutputInterface::VERBOSITY_NORMAL): void
    {
        if (null !== $this->output) {
            $debug = date('Y-m-d H:i:s') . ': ' . $message;
            $this->output->writeln($debug, OutputInterface::OUTPUT_NORMAL | $verbosity);
        }
    }
}
