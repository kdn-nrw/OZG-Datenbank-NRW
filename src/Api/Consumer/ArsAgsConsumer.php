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
        $totalUpdatedRowCount = 0;
        foreach ($communes as $commune) {
            /** @var Commune $commune */
            $communityKey = rtrim((string)$commune->getOfficialCommunityKey(), '0');
            $regionalKey = rtrim((string)$commune->getRegionalKey(), '0');
            /** @var ArsAgsDemand $demand */
            $demand = $this->getDemand();
            $demand->setSearchTerm($commune->getName());
            $this->dataProvider->setDemand($demand);
            $this->dataProvider->process($dataProcessor);
            $results = $this->dataProcessor->getResultCollection();
            /** @var ResultCollection $results */
            $bestMatchingResult = null;
            $bestNameMatch = 0;
            $communeTypeName = (string)$commune->getCommuneType();
            foreach ($results as $result) {

                $compCommunityKey = rtrim($result->getCommuneKey(), '0');
                $compRegionalKey = rtrim($result->getRegionalKey(), '0');
                $compName = $result->getName();
                $nameMatch = $commune->getName() === $result->getName() ? 100 : 0;
                if (!$nameMatch) {
                    $compName = trim(preg_replace('/\([\d\-]+\)/', '', $compName));
                    $nameMatch = $commune->getName() === $compName ? 50 : 0;
                    if (!$nameMatch) {
                        $nameParts = explode(',', $compName);
                        $nameMatch = $commune->getName() === trim($nameParts[0]) ? 25 : 0;
                    }
                    if (strpos($compName, $communeTypeName) !== false) {
                        $nameMatch += 10;
                    }
                }
                /** @var ArsAgsResult $result */
                if (null === $bestMatchingResult) {
                    $bestMatchingResult = $result;
                    $bestNameMatch = $nameMatch;
                } elseif ((!empty($communityKey) && $communityKey === $compCommunityKey)
                    || (!empty($regionalKey) && $regionalKey === $compRegionalKey)) {
                    $bestMatchingResult = $result;
                    break;
                } elseif ($nameMatch > $bestNameMatch
                    || (!empty($communityKey) && $compCommunityKey === $result->getCommuneKey())) {
                    $bestMatchingResult = $result;
                    $bestNameMatch = $nameMatch;
                }
            }
            if (null !== $bestMatchingResult) {
                // Update either community key or regional key, if keys don't match
                if (!empty($communityKey) && !empty($regionalKey)) {
                    $compCommunityKey = rtrim($bestMatchingResult->getCommuneKey(), '0');
                    $compRegionalKey = rtrim($bestMatchingResult->getRegionalKey(), '0');
                    if ($communityKey === $compCommunityKey) {
                        $commune->setRegionalKey($bestMatchingResult->getRegionalKey());
                    } elseif ($regionalKey === $compRegionalKey) {
                        $commune->setOfficialCommunityKey($bestMatchingResult->getCommuneKey());
                    }
                }
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
            //echo "Checked commune: " .$commune->getName() . ' [ID: '.$commune->getId().']' . "\n";
            $results->clear();
            $this->demand = null;
        }
        if ($totalUpdatedRowCount > 0) {
            $em->flush();
        }
        return $totalUpdatedRowCount;
    }
}
