<?php

namespace Oro\Bundle\EmailBundle\Filter;

use Oro\Bundle\EmailBundle\Form\Type\Filter\ChoiceOriginFolderFilterType;
use Oro\Bundle\FilterBundle\Filter\ChoiceFilter;
use Oro\Bundle\FilterBundle\Filter\FilterUtility;

/**
 * The filter by a folder for email messages.
 */
class ChoiceOriginFolderFilter extends ChoiceFilter
{
    /**
     * {@inheritdoc}
     */
    protected function getFormType()
    {
        return ChoiceOriginFolderFilterType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $formView = $this->getForm()->createView();
        $fieldView = $formView->children['value'];
        $choices = $fieldView->vars['choices'];

        $metadata = $this->getDefaultMetadata();
        $metadata['choices'] = $choices;
        $metadata['populateDefault'] = $formView->vars['populate_default'];
        if (!empty($formView->vars['default_value'])) {
            $metadata['placeholder'] = $formView->vars['default_value'];
        }
        if (!empty($formView->vars['null_value'])) {
            $metadata['nullValue'] = $formView->vars['null_value'];
        }
        if ($fieldView->vars['multiple']) {
            $metadata[FilterUtility::TYPE_KEY] = 'multiselect-originfolder';
        }

        return $metadata;
    }

    /**
     * @return array
     */
    protected function getDefaultMetadata()
    {
        $formView = $this->getForm()->createView();
        $typeView = $formView->children['type'];

        $defaultMetadata = [
            'name'    => $this->getName(),
            // use filter name if label not set
            'label'   => ucfirst($this->name),
            'choices' => $typeView->vars['choices']
        ];

        $metadata = array_diff_key(
            $this->get() ?: [],
            array_flip($this->util->getExcludeParams())
        );
        $metadata = $this->mapParams($metadata);
        $metadata = array_merge($defaultMetadata, $metadata);

        return $metadata;
    }
}
