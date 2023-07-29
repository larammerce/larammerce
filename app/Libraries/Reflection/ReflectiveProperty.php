<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:41 PM
 */

namespace App\Libraries\Reflection;


use JetBrains\PhpStorm\ArrayShape;
use ReflectionProperty;

class ReflectiveProperty extends ReflectiveAbstraction
{
    private string $class_name;
    private string $property_name;
    private ReflectionProperty $reflection_property;

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws \ReflectionException
     */
    public function __construct(string $class_name, string $property_name)
    {
        $this->class_name = $class_name;
        $this->property_name = $property_name;
        $this->reflection_property = new ReflectionProperty($this->class_name, $this->property_name);
        parent::__construct();
    }

    public function getComment(): string
    {
        return $this->reflection_property->getDocComment();
    }

    public function getClassName(): string
    {
        return $this->class_name;
    }

    public function setClassName(string $class_name)
    {
        $this->class_name = $class_name;
    }

    public function getPropertyName(): string
    {
        return $this->property_name;
    }

    public function setPropertyName(string $property_name)
    {
        $this->property_name = $property_name;
    }

    public function getReflectionProperty(): ReflectionProperty
    {
        return $this->reflection_property;
    }

    public function setReflectionProperty(ReflectionProperty $reflection_property)
    {
        $this->reflection_property = $reflection_property;
    }

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     */
    #[ArrayShape(['annotations' => "array[]"])]
    public function toArray(): array
    {
        return [
            'annotations' => $this->getAnnotationsArraySerialize()
        ];
    }
}
