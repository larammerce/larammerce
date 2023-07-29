<?php

namespace App\Console\Commands;

use App\Libraries\Reflection\Action;
use App\Libraries\Reflection\ReflectiveNamespace;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class TranslationFill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:fill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $validation_attributes = $this->getValidationAttributes();
        $logs_translations = $this->getLogsTranslations();
        $logs_attributes = $logs_translations["attributes"] ?? [];
        $logs_methods = $logs_translations["methods"] ?? [];
        $logs_classes = $logs_translations["classes"] ?? [];
        $logs_actions = $logs_translations["actions"] ?? [];

        $model_attributes = $this->getListOfModelAttributes();
        $controller_actions = $this->getListOfControllerActions();
        $classes = $this->getListOfClassNames();

        $result_attributes = $logs_attributes;
        foreach ($model_attributes as $model_attribute) {
            if (key_exists($model_attribute, $logs_attributes)) {
                $result_attributes[$model_attribute] = $logs_attributes[$model_attribute];
            } else if (key_exists($model_attribute, $validation_attributes)) {
                $result_attributes[$model_attribute] = $validation_attributes[$model_attribute];
            } else {
                $result_attributes[$model_attribute] = "None";
            }
        }

        $result_methods = [];
        $result_actions = [];
        foreach ($controller_actions as $controller_action) {
            $controller_action = Str::snake($controller_action);
            if (Str::startsWith($controller_action, "__"))
                continue;
            if (key_exists($controller_action, $logs_methods)) {
                $result_methods[$controller_action] = $logs_methods[$controller_action];
            } else {
                $result_methods[$controller_action] = "None";
            }

            if (key_exists($controller_action, $logs_actions)) {
                $result_actions[$controller_action] = $logs_actions[$controller_action];
            } else {
                $result_actions[$controller_action] = "None";
            }

        }

        $result_classes = [];
        foreach ($classes as $class) {
            if (key_exists($class, $logs_classes)) {
                $result_classes[$class] = $logs_classes[$class];
            } else {
                $result_classes[$class] = "None";
            }
        }

        $this->writeLogsTranslations([
            "attributes" => $result_attributes,
            "methods" => $result_methods,
            "classes" => $result_classes,
            "actions" => $result_actions
        ]);

        return 0;
    }

    public function getValidationAttributes()
    {
        $validation = require(base_path("resources/lang/fa/validation.php"));
        return $validation["attributes"] ?? [];
    }

    public function getLogsTranslations()
    {
        return require(base_path("resources/lang/fa/structures.php"));
    }

    public function writeLogsTranslations($translations)
    {
        $result = "<?php";
        $result .= "\nreturn ";
        $result .= var_export($translations, true);
        $result .= ";";

        $result = str_replace("array (", "[", $result);
        $result = str_replace(")", "]", $result);
        file_put_contents(base_path("resources/lang/fa/structures.php"), $result);
    }

    public function getListOfModelAttributes(): array
    {
        $result = [];
        foreach ((new ReflectiveNamespace("\\App\\Models"))->getReflectiveClasses() as $reflective_class) {
            try {
                $instance = app($reflective_class->getClassName());
                $result = array_merge($result, $instance->getFillable());
            } catch (BindingResolutionException $e) {
            }
        }
        return array_unique($result);
    }

    public function getListOfControllerActions(): array
    {
        $routes = Route::getRoutes()->get();
        $result = [];
        foreach ($routes as $route) {

            $action = $route->getAction();
            if (isset($action["namespace"]) and $action["namespace"] === "App\\Http\\Controllers\\Admin") {
                try {
                    $method_name = (Action::withAction($action["uses"]))->getMethodName();
                    $result[] = $method_name;
                } catch (Exception $e) {
                }
            }

        }
        return array_unique($result);
    }

    public function getListOfClassNames(): array
    {
        $result = array_values(get_controller_entity_names());
        $result = array_merge($result, array_values(get_model_entity_names()));
        return array_unique($result);
    }
}
