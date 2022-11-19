<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Action;

use App\Admin\Frontend\ContextFrontendAdminInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Search\SearchHandler;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class SearchAction
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var SearchHandler
     */
    private $searchHandler;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        Pool $pool,
        SearchHandler $searchHandler,
        Environment $twig
    )
    {
        $this->pool = $pool;
        $this->searchHandler = $searchHandler;
        $this->twig = $twig;
    }

    /**
     * The search action first render an empty page, if the query is set, then the template generates
     * some ajax request to retrieve results for each admin. The Ajax query returns a JSON response.
     *
     * @return JsonResponse|Response
     */
    public function __invoke(Request $request): Response
    {
        $displayAdmins = [];
        $adminServiceIds = $this->pool->getAdminServiceIds();
        foreach ($adminServiceIds as $adminServiceId) {
            $admin = $this->pool->getInstance($adminServiceId);
            if ($admin instanceof ContextFrontendAdminInterface) {
                $displayAdmins[$adminServiceId] = $admin;
            }
        }
        if (!$request->get('admin') || !$request->isXmlHttpRequest()) {
            return new Response($this->twig->render('Frontend/search.html.twig', [
                'base_template' => $request->isXmlHttpRequest() ?
                    '@SonataAdmin/ajax_layout.html.twig' :
                    'Frontend/Admin/base.html.twig',
                'admin_pool' => $this->pool,
                'query' => $request->get('q'),
                'display_admins' => $displayAdmins,
            ]));
        }

        try {
            $admin = $this->pool->getAdminByAdminCode($request->get('admin'));
        } catch (ServiceNotFoundException $e) {
            throw new \RuntimeException('Unable to find the Admin instance', $e->getCode(), $e);
        }

        if (!$admin instanceof AdminInterface) {
            throw new \RuntimeException('The requested service is not an Admin instance');
        }

        $results = [];

        $page = false;
        $total = false;
        if ($pager = $this->searchHandler->search(
            $admin,
            $request->get('q'),
            $request->get('page'),
            $request->get('offset')
        )) {
            $hasShowAction = $admin->hasRoute('show');

            /** @var \Sonata\AdminBundle\Datagrid\Pager $pager */
            foreach ($pager->getCurrentPageResults() as $result) {
                $link = null;
                if ($hasShowAction && $admin->hasAccess('show', $result)) {
                    $link = $admin->generateObjectUrl('show', $result);
                }
                $results[] = [
                    'label' => $admin->toString($result),
                    'link' => $link,
                    'id' => $admin->id($result),
                ];
            }
            $page = (int)$pager->getPage();
            $total = (int)$pager->countResults();
        }

        $response = new JsonResponse([
            'results' => $results,
            'page' => $page,
            'total' => $total,
        ]);
        $response->setPrivate();

        return $response;
    }
}
