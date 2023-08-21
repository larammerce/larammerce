<?php

namespace App\Helpers;

use Symfony\Component\Process\Process;

class UpgradeProjectHelper
{
    private const PID_FILE = "tmp/update.pid";
    private const LOG_FILE = "tmp/update.log";

    public static function getPidFilePath(): string {
        return base_path("data/" . self::PID_FILE);
    }

    public static function getLogFilePath(): string {
        return base_path("data/" . self::LOG_FILE);
    }

    public static function getPid(): ?int {
        $pid = null;
        if (file_exists(self::getPidFilePath())) {
            $pid = (int)file_get_contents(self::getPidFilePath());
        }
        return $pid;
    }

    public static function isRunning(): bool {
        $pid = self::getPid();

        //check if the pid is null
        if ($pid === null) {
            return false;
        }

        //check if the pid is not an integer
        if (!is_int($pid)) {
            return false;
        }

        $output = shell_exec("ps -p $pid");
        return str_contains($output, (string)$pid);
    }

    public static function stop(): bool {
        $pid = self::getPid();

        //check if the pid is null
        if ($pid === null) {
            return false;
        }

        //check if the pid is not an integer
        if (!is_int($pid)) {
            return false;
        }

        $output = shell_exec("kill -9 $pid");
        return str_contains($output, (string)$pid);
    }

    public static function start(
        string $larammerce_repo_address,
        string $larammerce_branch_name,
        string $larammerce_theme_repo_address,
        string $larammerce_theme_branch_name,
        bool   $only_core,
        bool   $only_theme
    ): bool {
        //check if the process is already running
        if (self::isRunning()) {
            return false;
        }

        $base_path = base_path();
        $script_path = base_path('scripts/bash/upgrade.sh');

        $command = [$script_path];

        if ($only_core) {
            $command[] = "--only-core";
        }

        if ($only_theme) {
            $command[] = "--only-theme";
        }

        $command[] = "--theme-repo=" . $larammerce_theme_repo_address;
        $command[] = "--core-repo=" . $larammerce_repo_address;
        $command[] = "--core-path=" . $base_path;

        if (strlen($larammerce_branch_name) > 0) {
            $command[] = "--core-branch=" . $larammerce_branch_name;
        }

        if (strlen($larammerce_theme_branch_name) > 0) {
            $command[] = "--theme-branch=" . $larammerce_theme_branch_name;
        }

        // run the process and save the PID in file
        return self::runProcess($command, $base_path);
    }

    private static function runProcess($command, $base_path): ?int {
        static::cleanLogFile();

        $process = new Process($command);
        $process->setEnv(['PATH' => static::getPathEnv(), 'ECOMMERCE_BASE_PATH' => $base_path]);
        $process->setWorkingDirectory($base_path);
        $process->setTimeout(3600);
        $process->start();
        $pid = $process->getPid();
        file_put_contents(self::getPidFilePath(), $pid);

        // write the output of the process on the log file line by line when the process is running
        $process->wait(function ($type, $buffer) {
            if (Process::ERR === $type) {
                file_put_contents(self::getLogFilePath(), "ERROR: " . $buffer, FILE_APPEND);
            } else {
                file_put_contents(self::getLogFilePath(), $buffer, FILE_APPEND);
            }
        });

        return $pid;
    }

    public static function tailLogFromLine(int $line_number): string {
        // return the log file from line $line_number to the end
        $log_file = self::getLogFilePath();
        return shell_exec("tail -n +$line_number $log_file") ?? "";
    }

    public static function cleanLogFile(): void {
        file_put_contents(self::getLogFilePath(), "");
    }

    private static function getPathEnv(): string {
        return "/usr/local/cpanel/3rdparty/lib/path-bin:" .
            "/usr/local/sbin:" .
            "/usr/local/bin:" .
            "/usr/sbin:" .
            "/usr/bin:" .
            "/sbin:" .
            "/bin:" .
            "/opt/cpanel/composer/bin:" .
            "/opt/bin:" .
            "/usr/local/jdk/bin:" .
            "/usr/kerberos/sbin:" .
            "/usr/kerberos/bin:" .
            "/usr/X11R6/bin:" .
            "/usr/local/bin";
    }
}
