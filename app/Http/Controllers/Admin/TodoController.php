<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enums\TodoStatus;
use App\Models\Todo;
use App\Utils\Common\History;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * @role(enabled=true)
 */
class TodoController extends BaseController
{
    /**
     * @role(super_user)
     */
    public function index(): Factory|View|Application
    {
        parent::setPageAttribute();
        $todos = Todo::paginate(Todo::getPaginationCount());
        return view("admin.pages.todo.index", compact("todos"));
    }

    /**
     * @role(super_user)
     */
    public function create(): Factory|View|Application
    {
        return view("admin.pages.todo.create");
    }

    /**
     * @role(super_user)
     * @rules(subject="required|min:10")
     */
    public function store(Request $request): RedirectResponse
    {
        $todo = Todo::create($request->only("subject"));
        return response()->redirectToRoute("admin.todo.index");
    }

    public function show(Todo $todo)
    {

    }

    /**
     * @role(super_user)
     */
    public function edit(Todo $todo): Factory|View|Application
    {
        $statuses = [];
        foreach (TodoStatus::values() as $value) {
            $statuses[$value] = trans("general.todo.status." . $value);
        }
        return view("admin.pages.todo.edit", compact("todo", "statuses"));
    }

    /**
     * @role(super_user)
     * @rules(subject="required|min:10", status="in:".\App\Models\Enums\TodoStatus::stringValues())
     */
    public function update(Request $request, Todo $todo): RedirectResponse
    {
        $todo->update($request->only("subject", "status"));
        return History::redirectBack();
    }

    public function destroy(Todo $todo): RedirectResponse
    {
        $todo->delete();
        return redirect()->back();
    }

    public function getModel(): ?string
    {
        return Todo::class;
    }
}
