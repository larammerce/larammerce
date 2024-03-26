<?php

namespace App\Jobs;

use App\Helpers\UpgradeProjectHelper;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpgradeProjectJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private string $larammerce_repo_address;
    private string $larammerce_branch_name;
    private string $larammerce_theme_repo_address;
    private string $larammerce_theme_branch_name;
    private bool $only_core;
    private bool $only_theme;


    /**
     * @throws Exception
     */
    public function __construct(
        string $larammerce_repo_address,
        string $larammerce_branch_name,
        string $larammerce_theme_repo_address,
        string $larammerce_theme_branch_name,
        bool   $only_core,
        bool   $only_theme
    ) {
        if (UpgradeProjectHelper::isRunning()) {
            throw new Exception("Upgrade project is running");
        }

        UpgradeProjectHelper::cleanLogFile();

        $this->larammerce_repo_address = $larammerce_repo_address;
        $this->larammerce_branch_name = $larammerce_branch_name;
        $this->larammerce_theme_repo_address = $larammerce_theme_repo_address;
        $this->larammerce_theme_branch_name = $larammerce_theme_branch_name;
        $this->only_core = $only_core;
        $this->only_theme = $only_theme;
        $this->queue = config('queue.names.admin');
    }

    public function handle() {
        UpgradeProjectHelper::start(
            $this->larammerce_repo_address,
            $this->larammerce_branch_name,
            $this->larammerce_theme_repo_address,
            $this->larammerce_theme_branch_name,
            $this->only_core,
            $this->only_theme
        );
    }
}
