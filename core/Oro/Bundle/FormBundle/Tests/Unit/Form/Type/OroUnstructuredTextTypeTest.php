<?php

namespace Oro\Bundle\FormBundle\Tests\Unit\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroUnstructuredTextType;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OroUnstructuredTextTypeTest extends FormIntegrationTestCase
{
    public function testConfigureOptions()
    {
        /** @var OptionsResolver|\PHPUnit\Framework\MockObject\MockObject $resolver */
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->at(1))
            ->method('setDefaults')
            ->with([
                'multiple' => true
            ]);

        $formType = new OroUnstructuredTextType();
        $formType->configureOptions($resolver);
    }

    public function testSubmit()
    {
        $formData = [
            'type' => 1,
            [
                'value' => ['val0', 'val1']
            ]
        ];
        $form = $this->factory->create(OroUnstructuredTextType::class);
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($formData, $form->getData());
    }
}
