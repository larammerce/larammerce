<?php

namespace App\Traits;

use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use App\Utils\Reflection\ReflectiveProperty;
use Illuminate\Support\Str;
use ReflectionException;

/**
 * Trait Inputable
 * @package App\Models\Traits
 */
trait Inputable
{
    /**
     * @throws AnnotationSyntaxException
     * @throws AnnotationBadScopeException
     * @throws AnnotationBadKeyException
     * @throws ReflectionException
     * @throws AnnotationNotFoundException
     */
    public function __get(string $name)
    {
        if(Str::endsWith($name, "_input_type")){
            $attr_name = preg_replace("/_input_type\$/", "", $name);
            return $this->getInputType($attr_name);
        }
        return null;
    }

    /**
     * @throws AnnotationSyntaxException
     * @throws AnnotationNotFoundException
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws ReflectionException
     */
    public function getInputType($name){
        $property = new ReflectiveProperty($this::class, $name);
        if($property->hasAnnotation("data")){
            $annotation = $property->getAnnotation("data");
            $annotation_properties = $annotation->getProperties();
            if(isset($annotation_properties["input_type"]))
                return $annotation_properties["input_type"];
        }
        return null;
    }

    /**
     * @throws AnnotationSyntaxException
     * @throws AnnotationNotFoundException
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws ReflectionException
     */
    public function getInputData(): array
    {
        $tmp_data = get_object_vars($this);
        $result = [];
        foreach ($tmp_data as $key => $value){
            $result[$key] = [
                "value" => $value,
                "type" => $this->getInputType($key)
            ];
        }
        return $result;
    }

    /**
     * @throws AnnotationSyntaxException
     * @throws AnnotationNotFoundException
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws ReflectionException
     */
    public function getInputRule($name){
        $property = new ReflectiveProperty($this::class, $name);
        if($property->hasAnnotation("rules")){
            $annotation = $property->getAnnotation("rules");
            $annotation_properties = $annotation->getProperties();
            if(isset($annotation_properties["input_rule"]))
                return $annotation_properties["input_rule"];
        }
        return null;
    }
}
