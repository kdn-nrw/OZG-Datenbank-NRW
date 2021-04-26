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

class EmailVariableModel
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type = 'string';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * EmailVariableModel constructor.
     * @param string $name
     * @param string $type
     * @param array $options
     */
    public function __construct(string $name, string $type, array $options)
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


}