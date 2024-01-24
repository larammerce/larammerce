<?php

namespace App\Http\Controllers\Admin;

use App\Services\Queue\QueueService;
use Illuminate\Http\RedirectResponse;

class QueueController extends BaseController
{

    public function __construct(
        protected QueueService $queueService
    )
    {
        parent::__construct();
    }

    public function index()
    {
        $queues = $this->queueService->getAllQueuesData();
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
        dd(request()->get('queue'),request()->get('toggle_status'));
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
