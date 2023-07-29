<?php

namespace App\Libraries\EnvFile;

use App\Interfaces\FileHandlerInterface;
use Illuminate\Support\Collection;

class EnvFileService {
    protected FileHandlerInterface $file_handler;

    public function __construct(FileHandlerInterface $file_reader) {
        $this->file_handler = $file_reader;
    }

    public function getMissingEnvVars(): Collection {
        $env = $this->getCurrentEnvVars();
        $env_example = $this->getExampleEnvVars();
        return $env_example->diffKeys($env);
    }

    public function getDeprecatedEnvVars(): Collection {
        $env = $this->getCurrentEnvVars();
        $env_example = $this->getExampleEnvVars();
        return $env->diffKeys($env_example);
    }

    public function getCurrentEnvVars(): Collection {
        return $this->file_handler->read(base_path('.env'));
    }

    public function getExampleEnvVars(): Collection {
        return $this->file_handler->read(base_path('.env.example'));
    }

    public function storeEnvVars(Collection $vars): void {
        $env_lines = $vars->map(function ($row) {
            if(isset($row["key"]) and isset($row["value"])) {
                return "{$row['key']}={$row['value']}";
            }else{
                return null;
            }
        })->filter(function($row){
            return !is_null($row);
        });

        $this->file_handler->write(base_path('.env'), $env_lines);
    }
}