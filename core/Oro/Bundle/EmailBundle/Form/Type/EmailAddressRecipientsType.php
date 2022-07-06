<?php

namespace Oro\Bundle\EmailBundle\Form\Type;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EmailBundle\Form\DataTransformer\EmailAddressRecipientsTransformer;
use Oro\Bundle\EmailBundle\Form\Model\Email;
use Oro\Bundle\EmailBundle\Provider\EmailRecipientsHelper;
use Oro\Bundle\FormBundle\Form\Type\Select2HiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type which can be used for autocomplete search any email recipient.
 */
class EmailAddressRecipientsType extends AbstractType
{
    const NAME = 'oro_email_email_address_recipients';

    /** @var ConfigManager */
    protected $cm;

    /**
     * @param ConfigManager $cm
     */
    public function __construct(ConfigManager $cm)
    {
        $this->cm = $cm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();
        $builder->addViewTransformer(
            new EmailAddressRecipientsTransformer()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (null === $view->parent) {
            return;
        }

        if (!array_key_exists('data', $view->parent->vars) || !$view->parent->vars['data'] instanceof Email) {
            return;
        }

        $email = $view->parent->vars['data'];
        $configs = [
            'route_parameters' => [
                'entityClass' => $email->getEntityClass(),
                'entityId'    => $email->getEntityId(),
            ]
        ];

        $view->vars['configs'] = array_merge_recursive($configs, $view->vars['configs']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tooltip'        => false,
            'error_bubbling' => false,
            'empty_data'     => [],
            'configs' => [
                'allowClear'         => true,
                'multiple'           => true,
                'separator'          => EmailRecipientsHelper::EMAIL_IDS_SEPARATOR,
                'route_name'         => 'oro_email_autocomplete_recipient',
                'type'               => 'POST',
                'minimumInputLength' => $this->cm->get('oro_email.minimum_input_length'),
                'per_page'           => 100,
                'containerCssClass'  => 'taggable-email',
                'tags'               => [],
                'component'          => 'email-recipients',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Select2HiddenType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return static::NAME;
    }
}
