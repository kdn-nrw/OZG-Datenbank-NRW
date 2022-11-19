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

namespace App\Controller;

use App\Entity\Base\HasDocumentsEntityInterface;
use App\Entity\Base\HasImageEntityInterface;
use Imagine\Exception\RuntimeException;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\FileBinary;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Trait ControllerDownloadTrait
 * @method createNotFoundException(string $message = 'Not Found', Throwable $previous = null): NotFoundHttpException
 */
trait ControllerDownloadTrait
{

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @required
     * @param FilterManager $filterManager
     */
    public function injectFilterManager(FilterManager $filterManager): void
    {
        $this->filterManager = $filterManager;
    }

    private function getFilterManager(): FilterManager
    {
        return $this->filterManager;
    }

    /**
     * Get the entity attachment as binary response
     *
     * @param Request $request
     * @param int|string|null $id
     * @param int|null $documentId
     * @return Response
     */
    public function downloadAction(Request $request, $id = null, int $documentId = null): Response
    {
        /** @noinspection SuspiciousAssignmentsInspection */
        $id = (int)$request->get($this->admin->getIdParameter());
        /** @noinspection SuspiciousAssignmentsInspection */
        $documentId = (int)$request->get('documentId');
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        //$this->checkParentChildAssociation($request, $object);

        $this->admin->checkAccess('show', $object);
        return $this->downloadAttachment($object, $documentId);
    }

    /**
     * Download image
     * @param HasImageEntityInterface $object
     * @param int $documentId
     * @return Response
     */
    protected function downloadImage(HasImageEntityInterface $object, int $documentId): Response
    {
        $image = $object->getImage();
        $imageFile = ($image) ? $image->getFile() : null;
        if (!$imageFile || !$imageFile->isReadable()) {
            throw $this->createNotFoundException(sprintf('unable to find the object image with type: %s', $documentId));
        }
        $mimeType = $imageFile->getMimeType();
        $fileBinary = new FileBinary(
            $imageFile->getRealPath(),
            $mimeType,
            substr(strrchr($mimeType, '/'), 1)
        );
        try {
            $filteredImage = $this->getFilterManager()->applyFilter($fileBinary, HasImageEntityInterface::FILTER_NAME_DEFAULT);
        } catch (RuntimeException $exception) {
            $filteredImage = $fileBinary;
        }
        //return new Base64FileResponse($filteredImage);
        $fileName = $imageFile->getFilename();
        if (method_exists($imageFile, 'getOriginalName')) {
            $fileName = $imageFile->getOriginalName();
        }
        $headers = array(
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        );
        return new Response($filteredImage->getContent(), 200, $headers);
    }

    /**
     * Download attachment file
     *
     * @param HasDocumentsEntityInterface $object
     * @param int $fileId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function downloadAttachment(HasDocumentsEntityInterface $object, int $fileId): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $documents = $object->getDocuments();
        $upload = null;
        foreach ($documents as $document) {
            if ($document->getId() === $fileId) {
                $upload = $document;
                break;
            }
        }
        $uploadFile = ($upload) ? $upload->getFile() : null;
        if (!$uploadFile || !$uploadFile->isReadable()) {
            throw $this->createNotFoundException(sprintf('unable to find the object file with id: %s', $fileId));
        }
        /** @var File|UploadedFile $uploadFile */
        $fileName = $uploadFile->getFilename();
        if (method_exists($upload, 'getOriginalName') && $originalName = $upload->getOriginalName()) {
            $fileName = $originalName;
        }
        return $this->file($uploadFile, $fileName);
    }
}