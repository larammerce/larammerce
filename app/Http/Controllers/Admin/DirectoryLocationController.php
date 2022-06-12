<?php

namespace App\Http\Controllers\Admin;

use App\Models\Directory;
use App\Models\DirectoryLocation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * @package App\Http\Controllers\Admin
 * @role(enabled=true)
 */
class DirectoryLocationController extends BaseController
{
    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(directory_id="required|exists:directories,id")
     */
    public function index(): Factory|View|Application
    {
        $directory = Directory::find(request()->get('directory_id'));
        parent::setPageAttribute($directory->id);
        $directory_locations = $directory->directoryLocations()->with("city", "state")->paginate(DirectoryLocation::getPaginationCount());
        return view('admin.pages.directory_location.index', compact('directory_locations', 'directory'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(directory_id="required|exists:directories,id")
     */
    public function create(): Factory|View|Application
    {
        $directory = Directory::find(request()->get('directory_id'));
        return view('admin.pages.directory_location.create', compact('directory'));
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     * @rules(directory_id="required|exists:directories,id", state_id="required|exists:states,id",
     *     city_id="exists:cities,id")
     */
    public function store(): RedirectResponse
    {
        $directory = Directory::find(request()->get("directory_id"));
        $directory->addDirectoryLocation(request()->all());
        return redirect()->to(route('admin.directory-location.index') . '?directory_id=' . $directory->id);
    }

    /**
     * @role(super_user, cms_manager, acc_manager)
     */
    public function destroy(DirectoryLocation $directory_location): RedirectResponse
    {
        $directory = $directory_location->directory;
        $directory->deleteDirectoryLocation($directory_location);
        return back();
    }


    public function getModel(): ?string
    {
        return DirectoryLocation::class;
    }
}
