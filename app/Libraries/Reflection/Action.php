<?php

namespace App\Libraries\Reflection;

use Illuminate\Http\Request;
use JsonSerializable;
use ReflectionException;

/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:29 PM
 */
class Action implements JsonSerializable
{
    /**
     * @var string
     */
    private $action;
    /**
     * @var ReflectiveClass
     */
    private $class;
    /**
     * @var ReflectiveMethod
     */
    private $method;

    /**
     * @var Request
     */
    private $request;

    /**
     * Action constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return Action
     */
    private static function newObject(): Action
    {
        return new self();
    }

    /**
     * @param string $action
     * @return Action
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public static function withAction(string $action): Action
    {
        $newObj = self::newObject();
        $newObj->action = $action;
        $newObj->construct();
        return $newObj;
    }

    /**
     * @param string $class_name
     * @param string $method_name
     * @return Action
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public static function withClassMethodNames(string $class_name,
                                                string $method_name): Action
    {
        $newObj = self::newObject();
        $newObj->action = $class_name . '@' . $method_name;
        $newObj->construct();
        return $newObj;
    }

    /**
     * @param Request $request
     * @return Action|bool
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    public static function withRequest(Request $request)
    {
        if ($request != null and $request->route() != null) {
            $new_obj = self::newObject();
            $new_obj->action = $request->route()->getActionName();
            $new_obj->request = $request;
            $new_obj->construct();
            return $new_obj;
        } else
            return false;
    }

    /**
     * @return void
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     * @throws ReflectionException
     */
    private function construct()
    {
        $this->class = new ReflectiveClass($this->getClassName());
        $this->method = new ReflectiveMethod($this->getClassName(), $this->getMethodName());
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        $parts = explode('@', $this->action);
        return $parts[0];
    }

    public function getMethodName()
    {
        $parts = explode('@', $this->action);
        return $parts[1];
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * @return ReflectiveClass
     */
    public function getClass(): ReflectiveClass
    {
        return $this->class;
    }

    /**
     * @param ReflectiveClass $class
     */
    public function setClass(ReflectiveClass $class)
    {
        $this->class = $class;
    }

    /**
     * @return ReflectiveMethod
     */
    public function getMethod(): ReflectiveMethod
    {
        return $this->method;
    }

    /**
     * @param ReflectiveMethod $method
     */
    public function setMethod(ReflectiveMethod $method)
    {
        $this->method = $method;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize(): array
    {
        $data = [
            'action' => $this->action,
            'class' => $this->class->jsonSerialize(),
            'method' => $this->method->jsonSerialize(),
        ];
        if (isset($this->request) and $this->request instanceof Request)
            $data['request'] = $this->request->toArray();
        return $data;
    }
}
