<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:41 PM
 */

namespace App\Utils\Reflection;


use ReflectionException;
use ReflectionProperty;

class ReflectiveProperty extends ReflectiveAbstraction
{
    /**
     * @var string
     */
    private $class_name;
    /**
     * @var string
     */
    private $property_name;
    /**
     * @var ReflectionProperty
     */
    private $reflection_property;

    /**
     * ReflectiveProperty constructor.
     * @param string $class_name
     * @param string $property_name
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

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->reflection_property->getDocComment();
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
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->property_name;
    }

    /**
     * @param string $property_name
     */
    public function setPropertyName(string $property_name)
    {
        $this->property_name = $property_name;
    }

    /**
     * @return ReflectionProperty
     */
    public function getReflectionProperty(): ReflectionProperty
    {
        return $this->reflection_property;
    }

    /**
     * @param ReflectionProperty $reflection_property
     */
    public function setReflectionProperty(ReflectionProperty $reflection_property)
    {
        $this->reflection_property = $reflection_property;
    }


    /**
     * @return array
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     */
    public function toArray(): array
    {
        return [
            'annotations' => $this->getAnnotationsArraySerialize()
        ];
    }
}
