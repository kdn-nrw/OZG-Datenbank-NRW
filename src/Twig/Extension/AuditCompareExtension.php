<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\Service\Mailer\InjectEmailTemplateManagerTrait;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Exception\NoValueException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Extension\AbstractExtension;
use Twig\TemplateWrapper;
use Twig\TwigFilter;

class AuditCompareExtension extends AbstractExtension
{
    use InjectEmailTemplateManagerTrait;


    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter(
                'app_view_element_compare',
                [$this, 'renderViewElementCompare'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }

    /**
     * render a compared view element.
     *
     * @param mixed $baseObject
     * @param mixed $compareObject
     * @param bool $onlyShowChanges
     *
     * @return string
     */
    public function renderViewElementCompare(
        Environment               $environment,
        FieldDescriptionInterface $fieldDescription,
                                  $baseObject,
                                  $compareObject,
                                  $onlyShowChanges = true
    )
    {
        $template = $this->getTemplate(
            $fieldDescription,
            '@SonataAdmin/CRUD/base_show_field.html.twig',
            $environment
        );

        try {
            $baseValue = $fieldDescription->getValue($baseObject);
        } catch (NoValueException $e) {
            // NEXT_MAJOR: Remove the try catch in order to throw the NoValueException.
            @trigger_error(
                'Accessing a non existing value is deprecated'
                . ' since sonata-project/admin-bundle 3.67 and will throw an exception in 4.0.',
                E_USER_DEPRECATED
            );

            $baseValue = null;
        }

        try {
            $compareValue = $fieldDescription->getValue($compareObject);
        } catch (NoValueException $e) {
            // NEXT_MAJOR: Remove the try catch in order to throw the NoValueException.
            @trigger_error(
                'Accessing a non existing value is deprecated'
                . ' since sonata-project/admin-bundle 3.67 and will throw an exception in 4.0.',
                E_USER_DEPRECATED
            );

            $compareValue = null;
        }

        if (is_array($baseValue) && empty($baseValue)){
            $baseValueOutput = '';
        } else {
            $baseValueOutput = $template->render([
                'admin' => $fieldDescription->getAdmin(),
                'field_description' => $fieldDescription,
                'value' => $baseValue,
                'object' => $baseObject,
            ]);
        }

        if (is_array($compareValue) && empty($compareValue)){
            $compareValueOutput = '';
        } else {
            $compareValueOutput = $template->render([
                'field_description' => $fieldDescription,
                'admin' => $fieldDescription->getAdmin(),
                'value' => $compareValue,
                'object' => $compareObject,
            ]);
        }

        // Compare the rendered output of both objects by using the (possibly) overridden field block
        $isDiff = trim(strip_tags($baseValueOutput)) !== trim(strip_tags($compareValueOutput));
        if ($isDiff || !$onlyShowChanges) {
            return $this->render($fieldDescription, $template, [
                'field_description' => $fieldDescription,
                'value' => $baseValue,
                'value_compare' => $compareValue,
                'is_diff' => $isDiff,
                'admin' => $fieldDescription->getAdmin(),
                'object' => $baseObject,
            ], $environment);
        }
        return '';
    }

    /**
     * Get template.
     *
     * @param string $defaultTemplate
     *
     * @return TemplateWrapper
     */
    protected function getTemplate(
        FieldDescriptionInterface $fieldDescription,
                                  $defaultTemplate,
        Environment               $environment
    )
    {
        $templateName = $fieldDescription->getTemplate() ?: $defaultTemplate;

        try {
            $template = $environment->load($templateName);
        } catch (LoaderError $e) {
            $template = $environment->load($defaultTemplate);
        }

        return $template;
    }

    private function render(
        FieldDescriptionInterface $fieldDescription,
        TemplateWrapper           $template,
        array                     $parameters,
        Environment               $environment
    ): string
    {
        $content = $template->render($parameters);

        if ($environment->isDebug()) {
            $commentTemplate = <<<'EOT'

<!-- START
    fieldName: %s
    template: %s
    compiled template: %s
    -->
    %s
<!-- END - fieldName: %s -->
EOT;

            return sprintf(
                $commentTemplate,
                $fieldDescription->getFieldName(),
                $fieldDescription->getTemplate(),
                $template->getSourceContext()->getName(),
                $content,
                $fieldDescription->getFieldName()
            );
        }

        return $content;
    }
}
