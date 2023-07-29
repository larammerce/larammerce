<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:41 PM
 */

namespace App\Libraries\Reflection;


use JetBrains\PhpStorm\ArrayShape;
use ReflectionException;
use ReflectionMethod;

class ReflectiveMethod extends ReflectiveAbstraction
{
    private string $class_name;
    private string $method_name;
    private ReflectionMethod $reflection_method;

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws \ReflectionException
     */
    public function __construct(string $class_name, string $method_name)
    {
        $this->class_name = $class_name;
        $this->method_name = $method_name;
        $this->reflection_method = new ReflectionMethod($this->class_name, $this->method_name);
        parent::__construct();
    }

    public function getComment(): string
    {
        return $this->reflection_method->getDocComment();
    }

    public function getClassName(): string
    {
        return $this->class_name;
    }

    public function setClassName(string $class_name)
    {
        $this->class_name = $class_name;
    }

    public function getMethodName(): string
    {
        return $this->method_name;
    }

    public function setMethodName(string $method_name)
    {
        $this->method_name = $method_name;
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflection_method;
    }

    public function setReflectionMethod(ReflectionMethod $reflection_method)
    {
        $this->reflection_method = $reflection_method;
    }

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public function getAction(): Action
    {
        return Action::withClassMethodNames($this->class_name, $this->method_name);
    }

    /**
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    #[ArrayShape(['action' => "string", 'annotations' => "array[]"])]
    public function toArray(): array
    {
        return [
            'action' => $this->getAction()->getAction(),
            'annotations' => $this->getAnnotationsArraySerialize()
        ];
    }
}
