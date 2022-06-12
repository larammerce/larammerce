<?php


namespace App\Utils\Reflection;


use Exception;
use App\Utils\ClassFinder\ClassFinder;
use Illuminate\Support\Str;

class ReflectiveNamespace
{
    private ?string $namespace;
    private array $class_names;
    private array $reflective_classes;

    /**
     * @throws Exception
     */
    public function __construct(?string $namespace = null)
    {
        $this->namespace = Str::startsWith($namespace, "\\") ? substr($namespace, 1) : $namespace;
        $this->class_names = ClassFinder::getClassesInNamespace($this->namespace);
        $this->reflective_classes = [];

        foreach ($this->class_names as $class_name) {
            $this->reflective_classes[] = new ReflectiveClass($class_name);
        }
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(?string $namespace): void
    {
        $this->namespace = $namespace;
    }

    public function getClassNames(): array
    {
        return $this->class_names;
    }

    public function setClassNames(array $class_names): void
    {
        $this->class_names = $class_names;
    }

    public function getReflectiveClasses(): array
    {
        return $this->reflective_classes;
    }

    public function setReflectiveClasses(array $reflective_classes): void
    {
        $this->reflective_classes = $reflective_classes;
    }
}
