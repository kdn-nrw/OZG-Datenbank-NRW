<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\ModelRegion;


use App\Controller\ControllerDownloadTrait;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\ModelRegion\ConceptQueryType;
use App\Entity\ModelRegion\ModelRegionProject;
use App\Entity\ModelRegion\ModelRegionProjectConceptQuery;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ModelRegionProjectAdminController
 */
class ModelRegionProjectAdminController extends CRUDController
{
    use ControllerDownloadTrait;
    use InjectManagerRegistryTrait;
    use ControllerProjectPdfExportTrait;

    /**
     * This method can be overloaded in your custom CRUD controller.
     * It's called from createAction.
     *
     * @param object $object
     *
     * @return Response|null
     */
    protected function preCreate(Request $request, $object)
    {
        if ($object instanceof ModelRegionProject) {
            $this->initializeConceptQueries($object);
        }
        return null;
    }

    /**
     * This method can be overloaded in your custom CRUD controller.
     * It's called from editAction.
     *
     * @param object $object
     *
     * @return Response|null
     */
    protected function preEdit(Request $request, $object)
    {
        if ($object instanceof ModelRegionProject) {
            $this->initializeConceptQueries($object);
        }
        return null;
    }

    /**
     * Create the fixed list of concept queries for the given project
     *
     * @param ModelRegionProject $modelRegionProject
     * @return void
     */
    private function initializeConceptQueries(ModelRegionProject $modelRegionProject)
    {
        $em = $this->getDoctrine()->getManager();
        $ctRepository = $em->getRepository(ConceptQueryType::class);
        /** @var ConceptQueryType[] $conceptQueryTypes */
        $conceptQueryTypes = $ctRepository->findBy([], ['position' => 'ASC', 'id' => 'ASC']);
        $hasChanges = false;
        /** @var ModelRegionProjectConceptQuery[] $projectReferences */
        $projectReferences = $modelRegionProject->getConceptQueries();
        $mapReferencesByType = [];
        $mapTypes = [];
        $isNewEntity = !$em->contains($modelRegionProject);
        foreach ($conceptQueryTypes as $qt) {
            $mapTypes[$qt->getId()] = $qt;
            $mapReferencesByType[$qt->getId()] = null;
        }
        foreach ($projectReferences as $ref) {
            if (null !== $queryType = $ref->getConceptQueryType()) {
                if (isset($mapTypes[$queryType->getId()])) {
                    $mapReferencesByType[$queryType->getId()] = $ref;
                } else {
                    $modelRegionProject->removeConceptQuery($ref);
                }
            }
        }
        foreach ($mapReferencesByType as $qtId => $ref) {
            if (null === $ref) {
                $newRef = new ModelRegionProjectConceptQuery();
                $newRef->setModelRegionProject($modelRegionProject);
                $newRef->setConceptQueryType($mapTypes[$qtId]);
                $hasChanges = true;
                $mapReferencesByType[$qtId] = $newRef;
                if (!$isNewEntity) {
                    $em->persist($newRef);
                }
            }
        }
        if ($hasChanges) {
            $collection = new ArrayCollection();
            $position = 1;
            foreach ($mapReferencesByType as $ref) {
                $ref->setPosition($position);
                $collection->add($ref);
                ++$position;
            }
            $modelRegionProject->setConceptQueries($collection);
            if (!$isNewEntity) {
                $em->flush();
            }
        }
    }
}
