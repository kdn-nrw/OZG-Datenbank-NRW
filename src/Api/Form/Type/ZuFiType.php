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

namespace App\Api\Form\Type;

use App\Api\Consumer\Model\ZuFiDemand;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ZuFiType
 */
class ZuFiType extends ApiConsumerSearchType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('customKey', ChoiceType::class, [
            'label' => 'app.api.zu_fi.custom_key',
            'choices' => array_flip(ZuFiDemand::CUSTOM_SERACH_KEYS),
            'required' => false,
            'attr' => [
                'placeholder' => 'app.api.zu_fi.custom_key',
            ],
            'choice_translation_domain' => false,
        ]);
    }
}
