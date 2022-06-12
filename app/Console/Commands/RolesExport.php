<?php

namespace App\Console\Commands;

use App\Models\SystemUser;
use App\Utils\Reflection\ReflectiveNamespace;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RolesExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:export';

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
     * @return int
     */
    public function handle(): int
    {
        $namespace = new ReflectiveNamespace("App\\Http\\Controllers\\Admin");
        $headers = $this->getHeaders();
        $data_table = [];
        $result_file_content = "";
        foreach ($namespace->getReflectiveClasses() as $reflective_class) {
            $entity_name = get_controller_entity_name($reflective_class->getClassName());
            $new_class = [];
            $result_file_content .= $this->getClassRow($entity_name, $headers) . "\n";
            foreach ($reflective_class->getMethods() as $reflective_method) {
                if ($reflective_method->hasAnnotation("role")) {
                    $new_method = [];
                    $permissions = $reflective_method->getAnnotation("role")->getPropertyNames();
                    foreach ($headers as $header) {
                        if (in_array($header, $permissions)) {
                            $new_method[] = 1;
                        } else {
                            $new_method[] = 0;
                        }
                    }
                    $method_name = Str::snake($reflective_method->getMethodName());
                    $result_file_content .= $this->getMethodRow($method_name, $new_method) . "\n";
                    $new_class[$method_name] = $new_method;
                }
            }
            $data_table[$entity_name] = $new_class;
        }

        file_put_contents(base_path("docs/roles.md"), $result_file_content);
        return CommandAlias::SUCCESS;
    }

    private function getHeaders(): array
    {
        $fillables = app(SystemUser::class)->getFillable();
        return array_values(array_map(function (string $fillable) {
            return str_replace("is_", "", $fillable);
        }, array_filter($fillables, function ($fillable) {
            return Str::startsWith($fillable, "is_");
        })));
    }

    private function getHeadRow($headers): string
    {
        $result_1 = "| عنوان فعالیت";
        $result_2 = "|----------";
        $count = count($headers);
        foreach ($headers as $index => $header) {
            $result_1 .= "| " . trans("structures.attributes.{$header}") . " " . ($index + 1 === $count ? "|" : "");
            $result_2 .= "|----------" . ($index + 1 === $count ? "|" : "");
        }
        return $result_1 . "\n" . $result_2;
    }

    private function getClassRow($entity_name, $headers)
    {
        $result = "\n# " . trans("structures.classes.{$entity_name}");

        return $result . "\n\n" . $this->getHeadRow($headers);
    }

    private function getMethodRow($method_name, $method_row)
    {
        $result = "| " . trans("structures.methods.{$method_name}");
        $count = count($method_row);
        foreach ($method_row as $index => $item) {
            if ($item === 0) {
                $result .= "|    ❌   " . ($index + 1 === $count ? "|" : "");
            } else {
                $result .= "|    ✅   " . ($index + 1 === $count ? "|" : "");
            }
        }

        return $result;
    }
}
