<?php
/**
 * Mindbase 3
 *
 * PHP version 7.2
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2020 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      https://www.brain-appeal.com/
 * @since     2020-02-07
 */

namespace BrainAppeal\CampusmatchApplicantBundle\Controller;

use BrainAppeal\CampusmatchApplicantBundle\Entity\Applicant;
use BrainAppeal\CampusmatchApplicantBundle\Entity\Image;
use Imagine\Exception\RuntimeException;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\FileBinary;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ApplicantAdminController
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2020 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      https://www.brain-appeal.com/
 * @since     2020-02-07
 *
 */
class ApplicantAdminController extends CRUDController
{

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @required
     * @param FilterManager $filterManager
     */
    public function injectFilterManager(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    private function getFilterManager(): FilterManager
    {
        return $this->filterManager;
    }

    /**
     * Get the applicant attachment as binary response
     *
     * @param int|string|null $id
     * @param string|null $type
     * @return Response
     */
    public function downloadAction($id = null, $type = null): Response
    {
        // This is strange, but used like this in \Sonata\AdminBundle\Controller\CRUDController::showAction too
        $request = $this->getRequest();
        /** @noinspection SuspiciousAssignmentsInspection */
        $id = (int)$request->get($this->admin->getIdParameter());
        /** @noinspection SuspiciousAssignmentsInspection */
        $type = (string)$request->get('type');
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        //$this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('show', $object);
        /** @var Applicant $object */
        if ($type === Image::TYPE || $type === Image::CACHE_TYPE) {
            return $this->downloadImage($object, $type);
        }
        return $this->downloadAttachment($object, $type);
    }

    /**
     * Download image
     * @param Applicant $object
     * @param string $type
     * @return Response
     */
    private function downloadImage(Applicant $object, string $type)
    {
        $image = $object->getImage();
        $imageFile = ($image) ? $image->getFile() : null;
        if (!$imageFile || !$imageFile->isReadable()) {
            throw $this->createNotFoundException(sprintf('unable to find the object image with type: %s', $type));
        }
        $mimeType = $imageFile->getMimeType();
        $fileBinary = new FileBinary(
            $imageFile->getRealPath(),
            $mimeType,
            substr(strrchr($mimeType, '/'), 1)
        );
        try {
            $filteredImage = $this->getFilterManager()->applyFilter($fileBinary, Image::FILTER_NAME_DEFAULT);
        } catch (RuntimeException $exception) {
            $filteredImage = $fileBinary;
        }
        //return new Base64FileResponse($filteredImage);
        $fileName = $imageFile->getFilename();
        if (method_exists($imageFile, 'getOriginalName')) {
            $fileName = $imageFile->getOriginalName();
        }
        $headers = array(
            'Content-Type'     => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"'
        );
        return new Response($filteredImage->getContent(), 200, $headers);
    }

    /**
     * Download attachment file
     *
     * @param Applicant $object
     * @param string $type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function downloadAttachment(Applicant $object, string $type)
    {
        $upload = $object->getUploadByType($type);
        $uploadFile = ($upload) ? $upload->getFile() : null;
        if (!$uploadFile || !$uploadFile->isReadable()) {
            throw $this->createNotFoundException(sprintf('unable to find the object file with type: %s', $type));
        }
        $fileName = $uploadFile->getFilename();
        if (method_exists($uploadFile, 'getOriginalName')) {
            $fileName = $uploadFile->getOriginalName();
        }
        return $this->file($uploadFile, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

}
