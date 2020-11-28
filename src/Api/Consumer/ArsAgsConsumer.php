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

namespace App\Api\Consumer;

use App\Api\Consumer\DataProcessor\DefaultApiDataProcessor;
use App\Api\Consumer\Model\ArsAgsDemand;
use App\Api\Consumer\Model\ArsAgsResult;
use App\Api\Form\Type\ArsAgsSearchType;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Repository\CommuneRepository;
use App\Entity\StateGroup\Commune;
use App\Import\Model\ResultCollection;

class ArsAgsConsumer extends AbstractApiConsumer
{
    use InjectManagerRegistryTrait;

    /**
     * @required
     * @param DefaultApiDataProcessor $dataProcessor
     */
    public function injectDataProcessor(DefaultApiDataProcessor $dataProcessor): void
    {
        $this->dataProcessor = $dataProcessor;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    protected function getDemandClass(): string
    {
        return ArsAgsDemand::class;
    }

    /**
     * Returns the search result template for this consumer
     * @return string
     */
    public function getResultTemplate(): string
    {
        return 'Vsm/Partials/Results/_ars-ags-results.html.twig';
    }

    /**
     * Returns the class name for the search form type
     *
     * @return string
     */
    public function getFormTypeClass(): string
    {
        return ArsAgsSearchType::class;
    }

    /**
     * Returns the class name for the result model
     *
     * @return string
     */
    public function getImportModelClass(): string
    {
        return ArsAgsResult::class;
    }

    /**
     * Import commune data
     * @param int $limit Limit the number of rows to be imported
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function importServiceResults(int $limit = 100): int
    {
        /** @var CommuneRepository $repository */
        $em = $this->getEntityManager();
        $em->getConfiguration()->setSQLLogger(null);
        $repository = $em->getRepository(Commune::class);
        $communes = $repository->findAllWithMissingKeys('modifiedAt', 'DESC');
        $dataProcessor = $this->dataProcessor;
        $this->dataProvider->setApiConsumerEntity($this->getApiConsumerEntity());
        $dataProcessor->setImportModelClass($this->getImportModelClass());
        $dataProcessor->setOutput($this->output);
        $dataProcessor->setImportSource($this->getImportSourceKey());
        /** @var ArsAgsDemand $demand */
        $demand = $this->getDemand();
        $totalUpdatedRowCount = 0;
        foreach ($communes as $commune) {
            /** @var Commune $commune */
            $communityKey = $commune->getOfficialCommunityKey();
            $regionalKey = $commune->getRegionalKey();
            $demand->setSearchTerm($commune->getName());
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->dataProvider->setDemand($demand);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->dataProvider->process($dataProcessor);
            $results = $this->dataProcessor->getResultCollection();
            /** @var ResultCollection $results */
            $bestMatchingResult = null;
            foreach ($results as $result) {
                /** @var ArsAgsResult $result */
                if (null === $bestMatchingResult) {
                    $bestMatchingResult = $result;
                } elseif ((!empty($communityKey) && $communityKey === $result->getCommuneKey())
                    || (!empty($regionalKey) && $regionalKey === $result->getRegionalKey())) {
                    $bestMatchingResult = $result;
                    break;
                } elseif ($commune->getName() === $result->getName()
                    || (!empty($communityKey) && rtrim($communityKey, '0') === $result->getCommuneKey())) {
                    $bestMatchingResult = $result;
                }
            }
            if (null !== $bestMatchingResult) {
                if (empty($communityKey)) {
                    $commune->setOfficialCommunityKey($bestMatchingResult->getCommuneKey());
                }
                if (empty($regionalKey)) {
                    $commune->setRegionalKey($bestMatchingResult->getRegionalKey());
                }
                ++$totalUpdatedRowCount;

                if ($limit && $totalUpdatedRowCount > $limit) {
                    break;
                }
            }
        }
        if ($totalUpdatedRowCount > 0) {
            $em->flush();
        }
        return $totalUpdatedRowCount;
    }
}
