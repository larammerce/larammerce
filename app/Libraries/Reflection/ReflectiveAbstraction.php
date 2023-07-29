<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/13/16
 * Time: 1:42 PM
 */

namespace App\Libraries\Reflection;


use Exception;
use JsonSerializable;

abstract class ReflectiveAbstraction implements JsonSerializable
{
    /**
     * @var Annotation[]
     */
    private $annotations;
    private $annotation_parser;

    /**
     * ReflectiveAbstraction constructor.
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     */
    public function __construct()
    {
        $this->annotations = [];
        $this->annotation_parser = new AnnotationParser($this->getComment());
        foreach ($this->annotation_parser->getTitles() as $annotation_title) {
            $this->annotations[$annotation_title] = $this->createAnnotationByTitle($annotation_title);
        }
    }

    public abstract function toArray();

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param bool $needs_array
     * @return Annotation[]
     */
    public function getAnnotations(bool $needs_array = false): array
    {
        if ($needs_array)
            return array_values($this->annotations);
        return $this->annotations;
    }

    /**
     * @param Annotation[] $annotations
     */
    public function setAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this);
    }

    /**
     * @return string
     */
    public abstract function getComment(): string;


    /**
     * @return array[]
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     */
    public function getAnnotationsArraySerialize(): array
    {
        $result = [];
        if ($this->annotations) {
            foreach ($this->annotations as $key => $value) {
                try {
                    $result[$key] = $this->annotation_parser->parseValue($key);
                } catch (AnnotationBadKeyException $e) {
                    throw new AnnotationBadKeyException("Bad Key passed for @{$key}:\n{$e->getMessage()}");
                }
            }
        }
        return $result;
    }

    /**
     * @param string $title
     * @return Annotation
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     */
    private function createAnnotationByTitle(string $title): Annotation
    {
        return new Annotation($title, $this->parseProperties($title));
    }

    /**
     * @param string $title
     * @return string[]
     * @throws AnnotationBadKeyException
     * @throws AnnotationBadScopeException
     * @throws AnnotationSyntaxException
     */
    private function parseProperties(string $title): array
    {
        $resultArray = [];
        try {
            $properties = $this->annotation_parser->parseValue($title);
            foreach ($properties as $key => $value) {
                $evaluated_value = "";
                if (strlen($value) > 0)
                    try {
                        eval("\$evaluated_value = {$value};");
                    } catch (Exception $e) {
                        throw new AnnotationSyntaxException("Syntax error in : {$value}. note:{$e->getMessage()}");
                    }

                $resultArray[$key] = $evaluated_value;
            }
        } catch (AnnotationBadKeyException $e) {
            throw new AnnotationBadKeyException("Bad Key passed for @{$title}:\n{$e->getMessage()}");
        }

        return $resultArray;
    }

    /**
     * @param string $title
     * @return bool
     */
    public function hasAnnotation(string $title): bool
    {
        return array_key_exists($title, $this->annotations);
    }


    /**
     * @param string $title
     * @return Annotation
     * @throws AnnotationNotFoundException
     */
    public function getAnnotation(string $title): Annotation
    {
        if ($this->hasAnnotation($title))
            return $this->annotations[$title];
        throw new AnnotationNotFoundException("There is no annotation named '{$title}' in \n{$this->getComment()}");
    }

    /**
     * @return AnnotationParser
     */
    public function getAnnotationParser(): AnnotationParser
    {
        return $this->annotation_parser;
    }
}
