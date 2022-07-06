<?php

namespace Oro\Bundle\WorkflowBundle\Acl\Extension;

use Oro\Bundle\SecurityBundle\Metadata\Label;
use Oro\Bundle\WorkflowBundle\Helper\WorkflowTranslationHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Represents a translatable transition name.
 */
class TransitionLabel extends Label
{
    /** transition name (from step -> to step) */
    protected const TRANSITION_LABEL_TEMPLATE = "%s (%s \u{2192} %s)";

    /** @var string */
    protected $toLabel;

    /** @var string */
    protected $fromLabel;

    /**
     * @param string      $transitionLabel
     * @param string|null $toStepLabel
     * @param string|null $fromStepLabel
     */
    public function __construct(string $transitionLabel, string $toStepLabel = null, string $fromStepLabel = null)
    {
        parent::__construct($transitionLabel);
        $this->toLabel = $toStepLabel;
        $this->fromLabel = $fromStepLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function trans(TranslatorInterface $translator)
    {
        $transition = $this->label;
        if ($transition) {
            $transition = $translator->trans($transition, [], WorkflowTranslationHelper::TRANSLATION_DOMAIN);
        }
        $fromStep = $this->fromLabel;
        if (null === $fromStep) {
            $fromStep = $translator->trans('(Start)', [], 'jsmessages');
        } elseif ($fromStep) {
            $fromStep = $translator->trans($fromStep, [], WorkflowTranslationHelper::TRANSLATION_DOMAIN);
        }
        $toStep = $this->toLabel;
        if ($toStep) {
            $toStep = $translator->trans($toStep, [], WorkflowTranslationHelper::TRANSLATION_DOMAIN);
        }

        return sprintf(self::TRANSITION_LABEL_TEMPLATE, $transition, $fromStep, $toStep);
    }


    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->label, $this->toLabel, $this->fromLabel]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->label, $this->toLabel, $this->fromLabel) = unserialize($serialized);
    }

    /**
     * The __set_state handler
     *
     * @param array $data Initialization array
     *
     * @return TransitionLabel A new instance of a TransitionLabel object
     */
    // @codingStandardsIgnoreStart
    public static function __set_state($data)
    {
        return new TransitionLabel($data['label'], $data['toLabel'], $data['fromLabel']);
    }
    // @codingStandardsIgnoreEnd
}
