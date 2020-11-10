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

namespace App\Api\Consumer\DataProvider;

use App\Api\Annotation\ApiSearchModelAnnotation;
use App\Api\Consumer\InvalidParametersException;
use App\Api\Consumer\Model\AbstractDemand;
use App\Entity\Api\ApiConsumer as ApiConsumerEntity;
use App\Import\Annotation\InjectAnnotationReaderTrait;
use App\Import\DataProcessor\DataProcessorInterface;
use App\Import\DataProvider\DataProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface DemandDataProviderInterface
{

    /**
     * Returns the API demand instance
     *
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand;
}
