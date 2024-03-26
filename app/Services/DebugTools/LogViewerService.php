<?php

namespace App\Services\DebugTools;

use App\Enums\DebugTools\DebugLogType;
use App\Exceptions\DebugTools\UnknownDebugLogTypeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use SplFileObject;

class LogViewerService
{
    /**
     * @throws UnknownDebugLogTypeException
     */
    public function getDebugTypePath(string $debug_log_type = DebugLogType::DEFAULT_LOG): string {
        return match ($debug_log_type) {
            DebugLogType::DEFAULT_LOG => "storage/logs",
            DebugLogType::DATA_TMP_LOG => "data/tmp",
            default => throw new UnknownDebugLogTypeException(),
        };
    }

    /**
     * @throws UnknownDebugLogTypeException
     */
    public function listFiles(string $debug_log_type = DebugLogType::DEFAULT_LOG): Collection {
        return collect(
            File::files($this->getLogPath(debug_log_type: $debug_log_type)))
            ->filter(function ($file) {
                return $file->getExtension() === 'log';
            })
            ->map(function ($file) {
                return $file->getFilename();
            });
    }

    /**
     * @throws UnknownDebugLogTypeException
     */
    public function getLastLines($file_name, $lines = 200, string $debug_log_type = DebugLogType::DEFAULT_LOG): array {
        $file_path = $this->getLogPath($file_name, $debug_log_type);
        $all_lines = File::lines($file_path);
        return array_slice($all_lines->all(), -$lines, $lines);
    }

    /**
     * @throws UnknownDebugLogTypeException
     */
    public function searchKeyword(
        string $file_name,
        string $keyword,
        string $debug_log_type = DebugLogType::DEFAULT_LOG,
        int    $line_count = 200
    ): array {
        $file_path = $this->getLogPath($file_name, $debug_log_type);
        $matching_lines = [];
        $stack_trace = "";
        $inside_section = false;  // A flag to check if we are inside a log section.

        // Use generator function to read file line by line
        $file = new SplFileObject($file_path);
        foreach ($file as $line) {
            $line = trim($line);  // Remove leading and trailing white spaces

            // Perform case-insensitive search
            if (stripos($line, $keyword) !== false) {
                $inside_section = true;
                $stack_trace .= $line . "\n";
            } elseif ($line === '') {
                if ($stack_trace) {
                    $matching_lines[] = $stack_trace;
                    $stack_trace = "";
                    $inside_section = false;  // Reset flag
                }
            } else {
                if ($inside_section) {
                    $stack_trace .= $line . "\n";
                }
            }
        }

        // If lineCount is specified, return only the latest $lineCount matching lines
        if ($line_count > 0) {
            $matching_lines = array_slice($matching_lines, -$line_count);
        }

        return $matching_lines;
    }

    /**
     * @throws UnknownDebugLogTypeException
     */
    public function getLogPath(string $file_name = "", string $debug_log_type = DebugLogType::DEFAULT_LOG): string {
        $log_path = $this->getDebugTypePath($debug_log_type);
        return base_path($log_path . "/" . $file_name);
    }

    public function getFileTypesTitles(): array {
        $result = [];
        foreach (DebugLogType::values() as $iter_type) {
            $result[$iter_type] = $this->getFileTypeTransKey($iter_type);
        }

        return $result;
    }

    public function getFileTypeTransKey(string $file_type): string {
        return "general.debug_log_type.{$file_type}";
    }

    public function secureFileName(string $file_name): bool {
        // Security check for fileName
        return !str_contains($file_name, "../") and !str_contains($file_name, "../");
    }
}
