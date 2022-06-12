<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/28/18
 * Time: 12:26 PM
 */

namespace App\Models\Traits;

use App\Utils\Reflection\ReflectiveProperty;
use Illuminate\Support\Str;

/**
 * Trait Inputable
 * @package App\Models\Traits
 */
trait Inputable
{
    /**
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws \App\Utils\Reflection\AnnotationBadScopeException
     * @throws \App\Utils\Reflection\AnnotationBadKeyException
     * @throws \ReflectionException
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
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
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
     * @throws \App\Utils\Reflection\AnnotationBadKeyException
     * @throws \App\Utils\Reflection\AnnotationBadScopeException
     * @throws \ReflectionException
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
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
     * @throws \App\Utils\Reflection\AnnotationBadKeyException
     * @throws \App\Utils\Reflection\AnnotationBadScopeException
     * @throws \ReflectionException
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
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
     * @throws \App\Utils\Reflection\AnnotationBadKeyException
     * @throws \App\Utils\Reflection\AnnotationBadScopeException
     * @throws \ReflectionException
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
