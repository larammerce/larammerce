<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:41 PM
 */

namespace App\Utils\Reflection;


use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

class ReflectiveClass extends ReflectiveAbstraction
{
    /**
     * @var string
     */
    private $class_name;
    /**
     * @var ReflectionClass
     */
    private $reflection_class;

    /**
     * ReflectiveClass constructor.
     * @param string $class_name
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public function __construct(string $class_name)
    {
        $this->class_name = $class_name;
        $this->reflection_class = new ReflectionClass($this->class_name);
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->reflection_class->getDocComment();
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class_name;
    }

    /**
     * @param string $class_name
     */
    public function setClassName(string $class_name)
    {
        $this->class_name = $class_name;
    }

    /**
     * @return ReflectionClass
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflection_class;
    }

    /**
     * @param ReflectionClass $reflection_class
     */
    public function setReflectionClass(ReflectionClass $reflection_class)
    {
        $this->reflection_class = $reflection_class;
    }

    /**
     * @return ReflectionMethod[]
     */
    private function reflectionMethods(): array
    {
        return $this->reflection_class->getMethods();
    }

    /**
     * @return ReflectionProperty[]
     */
    private function reflectionProperties(): array
    {
        return $this->reflection_class->getProperties();
    }


    /**
     * @return ReflectiveMethod[]
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public function getMethods(): array
    {
        $methods = [];
        foreach ($this->reflectionMethods() as $reflection_method) {
            $method = Action::withClassMethodNames($this->class_name, $reflection_method->name)->getMethod();
            $methods[] = $method;
        }
        return $methods;
    }


    /**
     * @return ReflectiveProperty[]
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public function getProperties(): array
    {
        $properties = [];
        foreach ($this->reflectionProperties() as $reflection_property) {
            $property = new ReflectiveProperty($this->class_name, $reflection_property->name);
            $properties[] = $property;
        }
        return $properties;
    }

    /**
     * @return string[]
     */
    public function getMethodNames(): array
    {
        $method_names = [];
        foreach ($this->reflectionMethods() as $reflection_method) {
            $method_names[] = $reflection_method->name;
        }
        return $method_names;
    }

    /**
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        $property_names = [];
        foreach ($this->reflectionProperties() as $reflection_property) {
            $property_names[] = $reflection_property->name;
        }
        return $property_names;
    }

    /**
     * @return array
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    function toArray(): array
    {
        $methods = [];
        foreach ($this->getMethods() as $method) {
            $methods[] = $method->jsonSerialize();
        }
        $properties = [];
        foreach ($this->getMethods() as $property) {
            $properties[] = $property->jsonSerialize();
        }

        return [
            'className' => $this->class_name,
            'annotations' => $this->getAnnotationsArraySerialize(),
            'methods' => $methods,
            'properties' => $properties
        ];
    }
}
