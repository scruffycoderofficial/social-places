<?php

/*
 * This file is a copy of {@see Symfony\Component\Validator\Constraints\AllValidator}
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 */

namespace Oro\Bundle\PlatformBundle\Validator\Constraints;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * The difference with Symfony constraint is that not loaded collection items are not validated.
 * @see \Symfony\Component\Validator\Constraints\All
 * @see \Symfony\Component\Validator\Constraints\AllValidator
 */
class ValidLoadedItemsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidLoadedItems) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\ValidLoadedItems');
        }

        if (null === $value) {
            return;
        }

        if (!is_array($value) && !$value instanceof \Traversable) {
            throw new UnexpectedTypeException($value, 'array or Traversable');
        }

        if ($value instanceof AbstractLazyCollection && !$value->isInitialized()) {
            return;
        }

        /** @var ExecutionContextInterface $context */
        $context = $this->context;
        $validator = $context->getValidator()->inContext($context);
        if ($value instanceof PersistentCollection) {
            $value = $value->unwrap();
        }
        foreach ($value as $key => $element) {
            $validator->atPath('[' . $key . ']')->validate($element, $constraint->constraints);
        }
    }
}
