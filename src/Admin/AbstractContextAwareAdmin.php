<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin;


use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

/**
 * Class AbstractContextAwareAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-02-11
 */
abstract class AbstractContextAwareAdmin extends AbstractAdmin implements ContextAwareAdminInterface
{
    protected $customShowFields = ['serviceSystems', 'laboratories', 'services', 'publishedSolutions', 'solutions', 'serviceProviders',];

    protected $appContext = ContextAwareAdminInterface::APP_CONTEXT_BE;

    /**
     * @param string $appContext
     */
    public function setAppContext(string $appContext): void
    {
        $this->appContext = $appContext;
    }

    /**
     * @return string
     */
    public function getAppContext(): string
    {
        return $this->appContext;
    }

    protected function isFrontend(): bool
    {
        return $this->appContext === ContextAwareAdminInterface::APP_CONTEXT_FE;
    }

    /**
     * @return array
     */
    public function getCustomShowFields(): array
    {
        return $this->customShowFields;
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $fields = parent::getExportFields();
        $excludeFields = $this->getExportExcludeFields();
        if (!empty($excludeFields)) {
            $fields = array_diff($fields, $excludeFields);
        }
        return $fields;
    }

    /**
     * Add- custom query condition for full text data grid filter field
     * @param DatagridMapper $datagridMapper
     */
    protected function addFullTextDatagridFilter(DatagridMapper $datagridMapper)
    {
        $modelManager = $this->getModelManager();
        $entityClass = $this->getClass();
        $appContext = $this->getAppContext();
        $datagridMapper
            ->add('fullText', CallbackFilter::class, [
                'callback' => static function($queryBuilder, $alias, $field, $value) use ($modelManager, $entityClass, $appContext) {
                    if (!$value['value']) {
                        return false;
                    }
                    /** @var \Sonata\DoctrineORMAdminBundle\Model\ModelManager $modelManager */
                    $indexRepository = $modelManager->getEntityManager(SearchIndexWord::class)->getRepository(SearchIndexWord::class);
                    /** @var SearchIndexRepository $indexRepository */
                    $matchingRecordIds = $indexRepository->findMatchingIndexRecords($entityClass, $appContext, $value['value']);
                    if (null !== $matchingRecordIds) {

                        $queryBuilder
                            ->andWhere( $alias . ' IN(:matchingRecordIds)')
                            ->setParameter('matchingRecordIds', $matchingRecordIds);

                        return true;
                    }
                    return false;
                },
                'label' => 'app.common.full_text_search',
                'field_type' => SearchType::class,
                'show_filter' => true,
            ]);
    }

    /**
     * @return array
     */
    protected function getExportExcludeFields(): array
    {
        return [];
    }

    public function getExportFormats()
    {
        return ['xlsx'];
    }
}
