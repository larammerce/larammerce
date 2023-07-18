<?php

namespace App\Http\Controllers\Admin;

use App\Services\Common\SSHService;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\SystemUpgrade\SystemUpgradeSettingService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpgradeController extends BaseController {

    public function index(Request $request): Factory|View|Application {
        $record = SystemUpgradeSettingService::getRecord();
        $larammerce_repo_address = $record->getLarammerceRepoAddress();
        $larammerce_theme_repo_address = $record->getLarammerceThemeRepoAddress();
        if ($request->session()->has("public_key")) {
            $public_key = $request->session()->get("public_key");
            return view("admin.pages.upgrade.index", compact("larammerce_theme_repo_address", "larammerce_repo_address", "public_key"));
        }
        return view("admin.pages.upgrade.index", compact("larammerce_theme_repo_address", "larammerce_repo_address"));
    }

    /**
     * @rules(larammerce_repo_address="required|regex:/^git@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9\-]+:[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-.]+\.git$/",
     *     larammerce_theme_repo_address="required|regex:/^git@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9\-]+:[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-.]+\.git$/")
     */
    public function saveConfig(Request $request) {
        $larammerce_repo_address = $request->get('larammerce_repo_address');
        $larammerce_theme_repo_address = $request->get('larammerce_theme_repo_address');

        $record = SystemUpgradeSettingService::getRecord();
        $record->setLarammerceRepoAddress($larammerce_repo_address);
        $record->setLarammerceThemeRepoAddress($larammerce_theme_repo_address);

        $domains = $this->extractDomains([$larammerce_repo_address, $larammerce_theme_repo_address]);

        if ($request->has("create_key")) {
            $public_key = SSHService::addSSHKey($domains);
        }

        try {
            SystemUpgradeSettingService::setRecord($record);
            if (isset($public_key)) {
                return History::redirectBack()->with("public_key", $public_key);
            } else {
                return History::redirectBack();
            }
        } catch (NotValidSettingRecordException $e) {
            SystemMessageService::addErrorMessage('system_messages.system_upgrade.invalid_record');
            return History::redirectBack()->withInput();
        }
    }

    public function doUpgrade(Request $request) {
        ini_set('output_buffering','off');
        ini_set('zlib.output_compression','off');
        $record = SystemUpgradeSettingService::getRecord();

        $only_theme = $request->input('only_theme');
        $only_core = $request->input('only_core');
        $base_path = base_path();
        $script_path = base_path('scripts/bash/upgrade.sh');

        $command = [$script_path];

        if ($only_core) {
            $command[] = "--only-core";
        }

        if ($only_theme) {
            $command[] = "--only-theme";
        }

        $command[] = "--theme-repo=" . $record->getLarammerceThemeRepoAddress();
        $command[] = "--core-repo=" . $record->getLarammerceRepoAddress();
        $command[] = "--core-path=" . $base_path;

        $process = new Process($command);
        $process->setEnv(['PATH' => $this->getPathEnv(), 'ECOMMERCE_BASE_PATH' => $base_path]);
        $process->setWorkingDirectory($base_path);
        $process->setTimeout(3600);
        $process->start();

        $response = response()->stream(function () use ($process) {
            while ($process->isRunning()) {
                $incremental_output = $process->getIncrementalOutput();
                $incremental_error_output = $process->getIncrementalErrorOutput();

                if (strlen($incremental_output) > 0) {
                    echo 'data: ' . $incremental_output . "\n";
                }

                if (strlen($incremental_error_output) > 0) {
                    echo 'data: ERROR: ' . $incremental_error_output . "\n";
                }

                if (ob_get_length()) {
                    ob_flush();
                }

                flush();
                sleep(1);
            }

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            echo 'data: ' . $process->getIncrementalOutput() . "\n";
            echo "data: END: \n\n";
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    private function extractDomains($repo_urls): array {
        $domains = [];

        foreach ($repo_urls as $url) {
            if (preg_match('/^git@([a-zA-Z0-9.-]+):.+$/', $url, $matches)) {
                $domains[] = $matches[1];
            }
        }

        return array_unique($domains);
    }

    private function getPathEnv() {
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

    public function getModel(): ?string {
        return null;
    }
}
