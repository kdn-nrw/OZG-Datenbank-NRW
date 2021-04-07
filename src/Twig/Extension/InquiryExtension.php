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

namespace App\Twig\Extension;

use App\Admin\Onboarding\InquiryAdmin;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use App\Service\InjectAdminManagerTrait;
use App\Service\Onboarding\InjectInquiryManagerTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InquiryExtension extends AbstractExtension
{
    use InjectAdminManagerTrait;
    use InjectInquiryManagerTrait;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('inquiry_url', [$this, 'generateInquiryUrl']),
            new TwigFunction('object_inquiry_message_count', [$this, 'countEntityInquiries']),
        ];
    }

    /**
     * Generates an inquiry url
     *
     * @param object|string $referenceSource
     * @param int|null $referenceId
     * @return string
     */
    public function generateInquiryUrl($referenceSource, ?int $referenceId): string
    {
        $sourceId = $referenceId;
        $sourceClass = is_object($referenceSource) ? get_class($referenceSource) : (string)$referenceSource;
        if (null === $referenceId && $referenceSource instanceof BaseEntityInterface) {
            $sourceId = $referenceSource->getId();
        }
        $inquiryAdmin = $this->adminManager->getAdminClassForEntityClass(Inquiry::class);
        if (null !== $inquiryAdmin) {
            /** @var InquiryAdmin $inquiryAdmin */
            return $inquiryAdmin->generateUrl('question', ['referenceSource' => $sourceClass, 'referenceId' => $sourceId]);
        }
        return '';
    }

    /**
     * @param BaseEntityInterface $entity
     * @param bool $onlyNew Count only new messages
     * @return int The nu,ber of messages
     */
    public function countEntityInquiries(BaseEntityInterface $entity, bool $onlyNew = true): int
    {
        return $this->inquiryManager->countEntityInquiries($entity, $onlyNew);
    }

}
