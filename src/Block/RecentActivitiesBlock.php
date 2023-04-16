<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Block;

use App\Admin\AbstractContextAwareAdmin;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\ModelRegion\ModelRegion;
use App\Entity\StateGroup\Commune;
use App\Entity\StateGroup\ServiceProvider;
use App\Entity\Statistics\LogEntry;
use App\Entity\User;
use App\Service\InjectAdminManagerTrait;
use App\Service\InjectCacheTrait;
use App\Service\Onboarding\InjectInquiryManagerTrait;
use App\Translator\InjectTranslatorTrait;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;


class RecentActivitiesBlock extends AbstractBlockService
{
    use InjectCacheTrait;
    use InjectManagerRegistryTrait;
    use InjectAdminManagerTrait;
    use InjectTranslatorTrait;
    use InjectInquiryManagerTrait;

    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param Environment $twig
     * @param Security $security
     */
    public function __construct(Environment $twig, Security $security)
    {
        parent::__construct($twig);
        $this->security = $security;
    }

    /**
     * Creates the block content
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $mode = (string)$blockContext->getSetting('mode');
        $isAdminMode = 'admin' === $mode;
        $user = $this->security->getUser();
        $limit = (int)$blockContext->getSetting('number');
        if (null !== $user) {
            /** @var User $user */
            $lastModified = $user->getUpdatedAt() ? $user->getUpdatedAt()->getTimestamp() : strtotime("today");
            $cacheKey = $mode . '_block_recent_activities_' . $lastModified . '_' . $user->getId();
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $items = $this->cache->get($cacheKey, function (ItemInterface $item) use ($user, $limit) {
                // Expires at midnight, so it will be loaded once the next time the user logs in
                $item->expiresAfter(86400 - (time() - strtotime("today")));
                return $this->createUserItems($user, $limit);
            });
        } else {
            $items = [];
        }
        $parameters = [
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'items' => $items,
            'isAdminMode' => $isAdminMode,
        ];
        $response = $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
        if ($isAdminMode) {
            $response
                ->setTtl(0)
                ->setPrivate();
        }
        return $response;
    }

    /**
     * Creates the item list for the user
     *
     * @param UserInterface $user
     * @param int $limit
     * @return array
     */
    private function createUserItems(UserInterface $user, int $limit): array
    {
        $items = [];
        /** @var User $user */
        $communes = $user->getCommunes();
        if ($communes->count() > 0) {
            /** @var AbstractContextAwareAdmin $admin */
            $admin = $this->adminManager->getAdminByEntityClass(Commune::class);
            $prefix = $this->translator->trans('app.common.recent_activities.my_commune');
            foreach ($communes as $commune) {
                $items['static_commune_' . $commune->getId()] = [
                    'titlePrefix' => $prefix,
                    'title' => $commune->getName(),
                    'url' => $admin->generateContextObjectUrl('show', $commune),
                    'isStatic' => true,
                ];
            }
        }
        $modelRegions = $user->getModelRegions();
        if ($modelRegions->count() > 0) {
            $admin = $this->adminManager->getAdminByEntityClass(ModelRegion::class);
            $prefix = $this->translator->trans('app.common.recent_activities.my_model_region');
            foreach ($modelRegions as $modelRegion) {
                $items['static_model_region_' . $modelRegion->getId()] = [
                    'titlePrefix' => $prefix,
                    'title' => $modelRegion->getName(),
                    'url' => $admin->generateContextObjectUrl('show', $modelRegion),
                    'isStatic' => true,
                ];
            }
        }
        $serviceProviders = $user->getServiceProviders();
        if ($serviceProviders->count() > 0) {
            $adminClass = $this->adminManager->getAdminClassForEntityClass(ServiceProvider::class);
            $admin = $this->adminManager->getAdminInstance($adminClass);
            $prefix = $this->translator->trans('app.common.recent_activities.my_service_provider');
            foreach ($serviceProviders as $serviceProvider) {
                $items['static_service_provider_' . $serviceProvider->getId()] = [
                    'titlePrefix' => $prefix,
                    'title' => $serviceProvider->getName(),
                    'url' => $admin->generateContextObjectUrl('show', $serviceProvider),
                    'isStatic' => true,
                ];
            }
        }
        $userMessages = $this->inquiryManager->findUserInquiries($user);
        if ($userMessages) {
            $prefix = $this->translator->trans('app.common.recent_activities.my_inquiries');
            foreach ($userMessages as $message) {
                $lines = array_filter(array_map('trim', explode("\n", $message['text'])));
                $text = implode(' ', $lines);
                if (strlen($text) > 75) {
                    $text = substr($text, 0, 72) . '...';
                }
                $titlePrefix = $prefix;
                if (!empty($message['createdByName'])) {
                    $titlePrefix .= ' ' . $message['createdByName'];
                }
                /** @var BaseEntityInterface $referenceObject */
                $referenceObject = $message['referencedObject'];
                $admin = $this->adminManager->getAdminByEntityClass($message['referenceSource']);
                if ($admin->hasRoute('showQuestions') && $admin->hasAccess('showQuestions', $referenceObject)) {
                    $items['static_inquiry_' . $message['id']] = [
                        'titlePrefix' => $titlePrefix,
                        'title' => $text,
                        'url' => $admin->generateContextObjectUrl('showQuestions', $referenceObject),
                        'isStatic' => true,
                    ];
                }
            }
        }
        /*
        if (count($items) < $limit) {
            $this->addShowEntries($user, $limit, $items);
        }
        if (count($items) < $limit) {
            $this->addDefaultEntries($user, $limit, $items);
        }*/
        return $items;
    }

    /**
     * Add log entries for detail views
     *
     * @param UserInterface|null $user
     * @param int $limit
     * @param array $items
     * /
    private function addShowEntries(?UserInterface $user, int $limit, array &$items): void
    {
        $repository = $this->getEntityManager()->getRepository(LogEntry::class);
        $queryBuilder = $repository->createQueryBuilder('l');
        $queryBuilder
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->andWhere('l.requestMethod = \'GET\'')
            ->andWhere('l.route NOT LIKE :excludeFrontend')
            ->setParameter('excludeFrontend', 'frontend_app%')
            ->andWhere('l.route LIKE \'%_show\'')
            ->orderBy('l.createdAt', 'DESC')
            ->distinct()
            ->setMaxResults($limit * 3);
        $results = $queryBuilder->getQuery()->execute();
        foreach ($results as $logEntry) {
            /** @var LogEntry $logEntry * /
            $this->addLogItem($logEntry, $items);
            if (count($items) >= $limit) {
                break;
            }
        }
    }*/

    /**
     * Add default log entries (exclude show entries)
     *
     * @param UserInterface|null $user
     * @param int $limit
     * @param array $items
     * /
    private function addDefaultEntries(?UserInterface $user, int $limit, array &$items): void
    {
        $repository = $this->getEntityManager()->getRepository(LogEntry::class);
        $queryBuilder = $repository->createQueryBuilder('l');
        $queryBuilder
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->andWhere('l.requestMethod = \'GET\'')
            ->andWhere('l.route NOT IN (:excludeRoutes)')
            ->setParameter('excludeRoutes', ['sonata_admin_dashboard'])
            ->andWhere('l.route NOT LIKE :excludeFrontend')
            ->setParameter('excludeFrontend', 'frontend_app%')
            ->andWhere('l.route NOT LIKE \'%_show\'')
            ->orderBy('l.createdAt', 'DESC')
            ->distinct()
            ->setMaxResults($limit * 3);
        $results = $queryBuilder->getQuery()->execute();
        foreach ($results as $logEntry) {
            /** @var LogEntry $logEntry * /
            $this->addLogItem($logEntry, $items);
            if (count($items) >= $limit) {
                break;
            }
        }
    }*/

    /**
     * Adds the given log entry to the item list
     * @param LogEntry $logEntry
     * @param array $items
     * /
    private function addLogItem(LogEntry $logEntry, array &$items): void
    {
        if ($route = $logEntry->getRoute()) {
            $url = null;
            $prefix = '';
            $pathInfo = $logEntry->getPathInfo();
            $key = $pathInfo;
            $title = $pathInfo;
            $attributes = $logEntry->getRequestAttributes();
            $parameters = $logEntry->getQueryParameters();
            if (!empty($attributes['_sonata_admin'])) {
                $admin = $this->adminManager->getAdminInstance($attributes['_sonata_admin']);
                $manager = $admin->getModelManager();
                $entity = null;
                if (!empty($attributes['slug'])) {
                    $entity = $manager->findOneBy($admin->getClass(), ['slug' => $attributes['slug']]);
                } elseif (!empty($attributes['id'])) {
                    $entity = $manager->findOneBy($admin->getClass(), ['id' => (int)$attributes['id']]);
                }
                if (null !== $entity) {
                    $translatorStrategy = $admin->getLabelTranslatorStrategy();
                    $objectLabel = $translatorStrategy->getLabel('object_name');
                    $prefix = $this->translator->trans($objectLabel);
                    $title = '' . $entity;
                    $url = $admin->generateContextObjectUrl('show', $entity);
                    $key = $route . '_' . $entity->getId();
                } else {
                    $title = $this->translator->trans($admin->getLabel());
                    $url = $admin->generateUrl('list', $parameters);
                    $key = $admin->getBaseRouteName() . '_list';
                }
            }
            if ($url && !isset($items[$key])) {
                $items[$key] = [
                    'titlePrefix' => $prefix,
                    'title' => $title,
                    'url' => $url,
                    'isStatic' => false,
                ];
            }
        }
    }*/

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 7,
            'mode' => 'admin',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fas fa-th-list',
            'class' => null,
            'code' => false,
            'code_public' => false,
            'template' => 'Block/recent-activities.html.twig',
        ]);
    }
}