<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DebugTools\DebugLogType;
use App\Exceptions\DebugTools\UnknownDebugLogTypeException;
use App\Services\DebugTools\LogViewerService;
use App\Utils\CMS\SystemMessageService;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DebugLogController extends BaseController
{
    protected LogViewerService $log_service;

    public function __construct(LogViewerService $logService) {
        $this->log_service = $logService;
        parent::__construct();
    }

    /**
     * @rules(debug_log_type="in:".\App\Enums\DebugTools\DebugLogType::stringValues())
     */
    public function index(Request $request): Factory|View|Application {
        $types = $this->log_service->getFileTypesTitles();
        $debug_log_type = $request->input('debug_log_type') ?? DebugLogType::DEFAULT_LOG;
        try {
            $files = $this->log_service->listFiles($debug_log_type);
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
            $files = [];
        }
        return view('admin.pages.debug-log.index', compact('files', "types", "debug_log_type"));
    }

    /**
     * @rules(debug_log_type="in:".\App\Enums\DebugTools\DebugLogType::stringValues())
     */
    public function search(Request $request): View|Factory|array|Application {
        $file_name = $request->input('file_name');
        $keyword = $request->input('keyword');
        $types = $this->log_service->getFileTypesTitles();
        $debug_log_type = $request->input('debug_log_type') ?? DebugLogType::DEFAULT_LOG;
        try {
            $files = $this->log_service->listFiles($debug_log_type);
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
            $files = [];
        }

        try {
            $stack_traces = $this->log_service->searchKeyword($file_name, $keyword, $debug_log_type);
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
            $stack_traces = [];
        }
        return view('admin.pages.debug-log.index', compact('stack_traces', 'types', 'debug_log_type', 'file_name', 'keyword', 'files'));
    }

    public function view(Request $request): Factory|View|Application {
        $file_name = $request->input('file_name');
        $types = $this->log_service->getFileTypesTitles();
        $debug_log_type = $request->input('debug_log_type') ?? DebugLogType::DEFAULT_LOG;
        try {
            $files = $this->log_service->listFiles($debug_log_type);
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
            $files = [];
        }
        try {
            $lines = $this->log_service->getLastLines($file_name, 200, $debug_log_type);
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
            $lines = [];
        }
        return view('admin.pages.debug-log.index', compact('lines', 'types', 'debug_log_type', 'files', 'file_name'));
    }

    public function download(Request $request): BinaryFileResponse|RedirectResponse {
        $debug_log_type = $request->input('debug_log_type') ?? DebugLogType::DEFAULT_LOG;
        $file_name = $request->input('filename');

        try {
            return response()->download($this->log_service->getLogPath($file_name, $debug_log_type));
        } catch (UnknownDebugLogTypeException $e) {
            SystemMessageService::addErrorMessage("messages.debug_log.unknown_debug_log_type_error");
        }
        return History::redirectBack();
    }

    public function getModel(): ?string {
        return null;
    }
}
