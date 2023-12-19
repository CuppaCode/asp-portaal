<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Claim;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Notification;

class TaskController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('task_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $tasks = Task::with(['user', 'claim', 'team'])->get();

        $users = User::get();

        $claims = Claim::get();

        $teams = Team::get();

        return view('admin.tasks.index', compact('claims', 'tasks', 'teams', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('task_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $claims = Claim::pluck('claim_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        
        return view('admin.tasks.create', compact('claims', 'users'));
    }
    
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->all());
        $user = $task->user;
        $claim = Claim::where('id', $request->claim_id)->get();
        
        $message = new \App\Notifications\TaskCreation($task, $claim, $user);
        Notification::route('mail', [
            $task->user->email => $task->user->name])->notify($message);

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $task->id]);
        }

        if($request->input('add-task-dashboard', 'true')) {
            return redirect()->back();
        } else {
            return redirect()->route('admin.tasks.index');
        }
        
    }

    public function edit(Task $task)
    {
        abort_if(Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $claims = Claim::pluck('claim_number', 'id')->prepend(trans('global.pleaseSelect'), '');

        $task->load('user', 'claim', 'team');

        return view('admin.tasks.edit', compact('claims', 'task', 'users'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->all());

        return redirect()->route('admin.tasks.index');
    }

    public function show(Task $task)
    {
        abort_if(Gate::denies('task_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->load('user', 'claim', 'team');

        return view('admin.tasks.show', compact('task'));
    }

    public function destroy(Task $task)
    {
        abort_if(Gate::denies('task_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $task->delete();

        return back();
    }

    public function massDestroy(MassDestroyTaskRequest $request)
    {
        $tasks = Task::find(request('ids'));

        foreach ($tasks as $task) {
            $task->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('task_create') && Gate::denies('task_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Task();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function quickUpdateStatus(Request $request)
    {
        $task = Task::find($request->task_id);

        $task->status = $request->new_status;

        $task->save();

        return response()->json(
            [
                'status' => $task->status,
                'type' => 'alert-success',
                'message' => 'Taak status is succesvol aangepast!'
            ], 200);
    }
}
