@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.task.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.tasks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.id') }}
                        </th>
                        <td>
                            {{ $task->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.task_number') }}
                        </th>
                        <td>
                            {{ $task->task_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.description') }}
                        </th>
                        <td>
                            {!! $task->description !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.user') }}
                        </th>
                        <td>
                            {{ $task->user->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.claim') }}
                        </th>
                        <td>
                            {{ $task->claim->claim_number ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.deadline_at') }}
                        </th>
                        <td>
                            {{ $task->deadline_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.task.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\Task::STATUS_SELECT[$task->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.tasks.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection