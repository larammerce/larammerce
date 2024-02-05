<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\QueueHelper;
use App\Interfaces\Repositories\JobRepository;
use App\Services\Queue\QueueService;
use Illuminate\Http\RedirectResponse;

class QueueController extends BaseController
{

    public function __construct(
        protected QueueService $queueService,
        protected JobRepository $jobRepository,
    )
    {
        parent::__construct();
    }

    public function index()
    {
        $queues = $this->queueService->getAllData();
        return view(
            'admin.pages.queue.index',
            compact('queues'
            ),
        );
    }

    /**
     * @rules(queue="required|string",toggle_status="required")
     * @return RedirectResponse
     */
    public function update(): RedirectResponse
    {
        $this->queueService->toggleState();
        return redirect()->back();
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return null;
    }
}
