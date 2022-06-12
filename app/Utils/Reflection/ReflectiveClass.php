<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:41 PM
 */

namespace App\Utils\Reflection;


use JetBrains\PhpStorm\ArrayShape;
use ReflectionClass;
use ReflectionException;

class ReflectiveClass extends ReflectiveAbstraction
{
    private string $class_name;
    private ReflectionClass $reflection_class;

    /**
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

    public function getComment(): string
    {
        return $this->reflection_class->getDocComment();
    }

    public function getClassName(): string
    {
        return $this->class_name;
    }

    public function setClassName(string $class_name)
    {
        $this->class_name = $class_name;
    }

    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflection_class;
    }

    public function setReflectionClass(ReflectionClass $reflection_class)
    {
        $this->reflection_class = $reflection_class;
    }

    private function reflectionMethods(): array
    {
        return $this->reflection_class->getMethods();
    }

    private function reflectionProperties(): array
    {
        return $this->reflection_class->getProperties();
    }


    /**
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

    public function getMethodNames(): array
    {
        $method_names = [];
        foreach ($this->reflectionMethods() as $reflection_method) {
            $method_names[] = $reflection_method->name;
        }
        return $method_names;
    }

    public function getPropertyNames(): array
    {
        $property_names = [];
        foreach ($this->reflectionProperties() as $reflection_property) {
            $property_names[] = $reflection_property->name;
        }
        return $property_names;
    }

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    #[ArrayShape(['className' => "string", 'annotations' => "array[]", 'methods' => "array", 'properties' => "array"])]
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

    public function usesTrait(string $trait_name): bool
    {
        return in_array($trait_name, $this->reflection_class->getTraitNames());
    }
}
