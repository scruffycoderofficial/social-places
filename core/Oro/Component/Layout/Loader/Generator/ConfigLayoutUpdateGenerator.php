<?php
declare(strict_types=1);

namespace Oro\Component\Layout\Loader\Generator;

use Oro\Component\Layout\Exception\SyntaxException;
use Oro\Component\Layout\ExpressionLanguage\ExpressionValidator;
use Oro\Component\Layout\LayoutManipulatorInterface;
use Oro\Component\Layout\Loader\Visitor\VisitorCollection;
use Oro\Component\PhpUtils\ReflectionClassHelper;

/**
 * Generate layout updates from config files
 */
class ConfigLayoutUpdateGenerator extends AbstractLayoutUpdateGenerator
{
    public const NODE_ACTIONS = 'actions';

    public const PATH_ATTR = '__path';

    /** @var ConfigLayoutUpdateGeneratorExtensionInterface[] */
    protected $extensions = [];

    protected ?ReflectionClassHelper $helper = null;

    private ExpressionValidator $expressionValidator;

    public function __construct(ExpressionValidator $expressionValidator)
    {
        $this->expressionValidator = $expressionValidator;
    }

    public function addExtension(ConfigLayoutUpdateGeneratorExtensionInterface $extension)
    {
        $this->extensions[] = $extension;
    }

    protected function doGenerateBody(GeneratorData $data): string
    {
        $body   = [];
        $source = $data->getSource();

        foreach ($source[self::NODE_ACTIONS] as $actionDefinition) {
            $actionName = key($actionDefinition);
            $arguments  = isset($actionDefinition[$actionName]) && is_array($actionDefinition[$actionName])
                ? $actionDefinition[$actionName] : [];

            $call = [];
            $this->normalizeActionName($actionName);
            $this->getHelper()->completeArguments($actionName, $arguments);

            \array_walk(
                $arguments,
                static function (&$arg) {
                    $arg = var_export($arg, true);
                }
            );
            $call[] = \sprintf('$%s->%s(', self::PARAM_LAYOUT_MANIPULATOR, $actionName);
            $call[] = \implode(', ', $arguments);
            $call[] = ');';

            $body[] = \implode(' ', $call);
        }

        return \implode("\n", $body);
    }

    /**
     * Validates given resource data, checks that "actions" node exists and consist valid actions.
     *
     * @throws SyntaxException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function validate(GeneratorData $data): void
    {
        $source = $data->getSource();

        if (!(\is_array($source) && isset($source[self::NODE_ACTIONS]) && \is_array($source[self::NODE_ACTIONS]))) {
            throw new SyntaxException(sprintf('expected array with "%s" node', self::NODE_ACTIONS), $source);
        }

        $actions = $source[self::NODE_ACTIONS];
        foreach ($actions as $nodeNo => $actionDefinition) {
            if (isset($actionDefinition[self::PATH_ATTR])) {
                $path = $actionDefinition[self::PATH_ATTR];
                unset($actionDefinition[self::PATH_ATTR]);
            } else {
                $path = self::NODE_ACTIONS . '.' . $nodeNo;
            }

            if (!\is_array($actionDefinition)) {
                throw new SyntaxException('expected array with action name as key', $actionDefinition, $path);
            }

            $actionName = \key($actionDefinition);
            $arguments  = \is_array($actionDefinition[$actionName])
                ? $actionDefinition[$actionName] : [$actionDefinition[$actionName]];

            if (0 !== \strpos($actionName, '@')) {
                throw new SyntaxException(
                    \sprintf('action name should start with "@" symbol, current name "%s"', $actionName),
                    $actionDefinition,
                    $path
                );
            }

            $this->normalizeActionName($actionName);

            if (!$this->getHelper()->hasMethod($actionName)) {
                throw new SyntaxException(
                    \sprintf('unknown action "%s", should be one of LayoutManipulatorInterface methods', $actionName),
                    $actionDefinition,
                    $path
                );
            }

            if (!$this->getHelper()->isValidArguments($actionName, $arguments)) {
                throw new SyntaxException($this->getHelper()->getLastError(), $actionDefinition, $path);
            }
        }

        $this->validateExpressionsRecursive($source);
    }

    protected function prepare(GeneratorData $data, VisitorCollection $visitorCollection): void
    {
        foreach ($this->extensions as $extension) {
            $extension->prepare($data, $visitorCollection);
        }
    }

    protected function getHelper(): ReflectionClassHelper
    {
        if (null === $this->helper) {
            $this->helper = new ReflectionClassHelper(LayoutManipulatorInterface::class);
        }

        return $this->helper;
    }

    /**
     * Removes "@" sign from beginning of action name
     *
     * @param string $actionName
     */
    protected function normalizeActionName(&$actionName)
    {
        $actionName = \substr($actionName, 1);
    }

    private function validateExpressionsRecursive(array $source, ?string $path = null): void
    {
        if ($path) {
            $path .= '.';
        }

        foreach ($source as $key => $value) {
            if (!$value) {
                continue;
            }

            if (\is_array($value)) {
                $this->validateExpressionsRecursive($value, $path . $key);
                continue;
            }

            if (\is_string($value) && '=' === $value[0]) {
                try {
                    $this->expressionValidator->validate(\substr($value, 1));
                } catch (\Throwable $e) {
                    throw new SyntaxException($e->getMessage(), $source, $path . $key, $e);
                }
            }
        }
    }
}
