<?php

namespace Oro\Bundle\SecurityBundle\Metadata;

use Oro\Bundle\SecurityBundle\Annotation\Acl as AclAnnotation;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor as AclAnnotationAncestor;

/**
 * The storage for ACL annotations and bindings.
 */
class AclAnnotationStorage implements \Serializable
{
    /** @var AclAnnotation[] [annotation id => annotation object, ...] */
    private $annotations = [];

    /** @var array [class name => [method name ('!' for class if it have an annotation) => annotation id, ...], ...] */
    private $classes = [];

    /**
     * Gets an annotation by its id
     *
     * @param  string                    $id
     * @return AclAnnotation|null        AclAnnotation object or null if ACL annotation was not found
     * @throws \InvalidArgumentException
     */
    public function findById($id)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('$id must not be empty.');
        }

        return $this->annotations[$id] ?? null;
    }

    /**
     * Gets an annotation bound to the given class/method
     *
     * @param  string                    $class
     * @param  string|null               $method
     * @return AclAnnotation|null        AclAnnotation object or null if ACL annotation was not found
     * @throws \InvalidArgumentException
     */
    public function find($class, $method = null)
    {
        if (empty($class)) {
            throw new \InvalidArgumentException('$class must not be empty.');
        }

        if (empty($method)) {
            if (!isset($this->classes[$class]['!'])) {
                return null;
            }
            $id = $this->classes[$class]['!'];
        } else {
            if (!isset($this->classes[$class][$method])) {
                return null;
            }
            $id = $this->classes[$class][$method];
        }

        return $this->annotations[$id] ?? null;
    }

    /**
     * Determines whether the given class/method has an annotation
     *
     * @param  string      $class
     * @param  string|null $method
     * @return bool
     */
    public function has($class, $method = null)
    {
        if (empty($method)) {
            if (!isset($this->classes[$class]['!'])) {
                return false;
            }
            $id = $this->classes[$class]['!'];
        } else {
            if (!isset($this->classes[$class][$method])) {
                return false;
            }
            $id = $this->classes[$class][$method];
        }

        return isset($this->annotations[$id]);
    }

    /**
     * Gets annotations
     *
     * @param  string|null     $type The annotation type
     * @return AclAnnotation[]
     */
    public function getAnnotations($type = null)
    {
        if ($type === null) {
            return \array_values($this->annotations);
        }

        $result = [];
        foreach ($this->annotations as $annotation) {
            if ($annotation->getType() === $type) {
                $result[] = $annotation;
            }
        }

        return $result;
    }

    /**
     * Checks whether the given class is registered in this storage
     *
     * @param  string $class
     * @return bool   true if the class is registered in this storage; otherwise, false
     */
    public function isKnownClass($class)
    {
        return isset($this->classes[$class]);
    }

    /**
     * Checks whether the given method is registered in this storage
     *
     * @param  string $class
     * @param  string $method
     * @return bool   true if the method is registered in this storage; otherwise, false
     */
    public function isKnownMethod($class, $method)
    {
        return isset($this->classes[$class][$method]);
    }

    /**
     * Adds an annotation
     *
     * @param  AclAnnotation             $annotation
     * @param  string|null               $class
     * @param  string|null               $method
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function add(AclAnnotation $annotation, $class = null, $method = null)
    {
        $id = $annotation->getId();
        $this->annotations[$id] = $annotation;
        if ($class !== null) {
            $this->addBinding($id, $class, $method);
        }
    }

    /**
     * Adds an annotation ancestor
     *
     * @param  AclAnnotationAncestor     $ancestor
     * @param  string|null               $class
     * @param  string|null               $method
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function addAncestor(AclAnnotationAncestor $ancestor, $class = null, $method = null)
    {
        if ($class !== null) {
            $this->addBinding($ancestor->getId(), $class, $method);
        }
    }

    /**
     * Gets bindings for class.
     *
     * @param string $class
     *
     * @return array [method name => annotation, ...]
     */
    public function getBindings(string $class): array
    {
        return $this->classes[$class] ?? [];
    }

    /**
     * Removes bindings for class.
     *
     * @param string $class
     */
    public function removeBindings(string $class): void
    {
        unset($this->classes[$class]);
    }

    /**
     * Removes an annotation binding.
     *
     * @param string      $class
     * @param string|null $method
     */
    public function removeBinding(string $class, string $method = null): void
    {
        if (empty($class)) {
            throw new \InvalidArgumentException('$class must not be empty.');
        }

        if (empty($method)) {
            $method = '!';
        }

        if (isset($this->classes[$class][$method])) {
            unset($this->classes[$class][$method]);
        }
    }

    /**
     * Adds an annotation binding
     *
     * @param  string                    $id
     * @param  string                    $class
     * @param  string|null               $method
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addBinding($id, $class, $method = null)
    {
        if (empty($class)) {
            throw new \InvalidArgumentException('$class must not be empty.');
        }

        if (isset($this->classes[$class])) {
            if (empty($method)) {
                if (isset($this->classes[$class]['!']) && $this->classes[$class]['!'] !== $id) {
                    throw new \RuntimeException(\sprintf(
                        'Duplicate binding for "%s". New Id: %s. Existing Id: %s',
                        $class,
                        $id,
                        $this->classes[$class]['!']
                    ));
                }
                $this->classes[$class]['!'] = $id;
            } else {
                if (isset($this->classes[$class][$method]) && $this->classes[$class][$method] !== $id) {
                    throw new \RuntimeException(\sprintf(
                        'Duplicate binding for "%s". New Id: %s. Existing Id: %s',
                        $class . '::' . $method,
                        $id,
                        $this->classes[$class][$method]
                    ));
                }
                $this->classes[$class][$method] = $id;
            }
        } elseif (empty($method)) {
            $this->classes[$class] = ['!' => $id];
        } else {
            $this->classes[$class] = [$method => $id];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $data = [];
        foreach ($this->annotations as $annotation) {
            $data[] = $annotation->serialize();
        }

        return \serialize([$data, $this->classes]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($data, $this->classes) = \unserialize($serialized);

        $this->annotations = [];
        foreach ($data as $d) {
            $annotation = new AclAnnotation();
            $annotation->unserialize($d);
            $this->annotations[$annotation->getId()] = $annotation;
        }
    }

    /**
     * The __set_state handler
     *
     * @param array $data Initialization array
     * @return AclAnnotationStorage A new instance of a AclAnnotationStorage object
     */
    // @codingStandardsIgnoreStart
    public static function __set_state($data)
    {
        $result = new AclAnnotationStorage();
        $result->annotations = $data['annotations'];
        $result->classes = $data['classes'];

        return $result;
    }
    // @codingStandardsIgnoreEnd
}
