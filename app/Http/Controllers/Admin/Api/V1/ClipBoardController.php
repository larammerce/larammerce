<?php
/**
 */

namespace App\Http\Controllers\Admin\Api\V1;

use App\Helpers\ResponseHelper;
use App\Utils\CMS\File\ClipBoardService;
use App\Utils\CMS\File\EmptyClipBoardException;
use App\Utils\CMS\File\EmptyObjectCollectionException;
use App\Utils\CMS\File\FileBadDestinationException;
use App\Utils\CMS\File\FileManager;
use App\Utils\CMS\File\FileSameDestinationException;
use App\Utils\CMS\File\InvalidActionException;
use App\Utils\CMS\File\InvalidIdsException;
use App\Utils\CMS\File\InvalidTypeException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Http\Controllers\Admin\Api\V1
 * @role(enabled=true)
 */
class ClipBoardController extends BaseController
{

    /**
     * @rules(type="required|in:".\App\Utils\CMS\File\FileType::stringValues(),
     *      ids.*="required_with:type|exists:".App\Helpers\EloquentModelHelper::getTableName(request("type")).",id")
     * @role(super_user, cms_manager)
     */
    public function doCut(): JsonResponse
    {
        try {
            $tickedFilesList = request()->only("action", "type", "ids");
            ClipBoardService::setFiles($tickedFilesList);
            return response()->json(ResponseHelper::create(['system_messages.clip_board.cut.done'], 200));
        } catch (InvalidActionException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.cut.invalid_file_action'], 400), 400);
        } catch (InvalidTypeException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.cut.invalid_file_type'], 400), 400);
        } catch (InvalidIdsException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.cut.invalid_file_ids'], 400), 400);
        } catch (Exception $exception) {
            Log::info($exception->getFile() . " " . $exception->getMessage() . " " . $exception->getLine());
            return response()->json(ResponseHelper::create(['system_messages.clip_board.cut.failed'], 400), 400);
        }
    }

    /**
     * @rules(type="required|in:".\App\Utils\CMS\File\FileType::stringValues(),
     *      ids.*="required_with:type|exists:".App\Helpers\EloquentModelHelper::getTableName(request('type')).",id")
     * @role(super_user, cms_manager)
     */
    public function doCopy(): JsonResponse
    {
        try {
            $tickedFilesList = request()->only("action", "type", "ids");
            ClipBoardService::setFiles($tickedFilesList);
            return response()->json(ResponseHelper::create(['system_messages.clip_board.copy.done'], 200));
        } catch (InvalidActionException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.copy.invalid_file_action'], 400), 400);
        } catch (InvalidTypeException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.copy.invalid_file_type'], 400), 400);
        } catch (InvalidIdsException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.copy.invalid_file_ids'], 400), 400);
        } catch (Exception $exception) {
            Log::info($exception->getFile() . " " . $exception->getMessage() . " " . $exception->getLine());
            return response()->json(ResponseHelper::create(['system_messages.clip_board.copy.failed', $exception->getMessage()], 400), 400);
        }
    }

    /**
     * @rules(directory_id="exists:directories,id")
     * @role(super_user, cms_manager)
     */
    public function doPaste(): JsonResponse
    {
        try {
            FileManager::paste(request('directory_id'));
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.done'], 200));
        } catch (EmptyClipBoardException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.empty_clip_board'], 400), 400);
        } catch (EmptyObjectCollectionException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.empty_file_object_collection'], 400), 400);
        } catch (InvalidTypeException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.invalid_file_type'], 400), 400);
        } catch (FileSameDestinationException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.file_same_destination'], 400), 400);
        } catch (FileBadDestinationException $exception) {
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.file_bad_destination'], 400), 400);
        } catch (Exception $exception) {
            Log::info($exception->getFile() . " " . $exception->getMessage() . " " . $exception->getLine());
            return response()->json(ResponseHelper::create(['system_messages.clip_board.paste.failed'], 400), 400);
        }
    }

    public function getModel(): ?string
    {
        return null;
    }
}
