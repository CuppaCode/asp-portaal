@extends('layouts.admin')
@section('content')

<div class="top-bar-claims form-group d-flex justify-content-between align-items-center">
    <a class="btn btn-dark" href="{{ route('admin.claims.index') }}">
        {{ trans('global.back_to_list') }}
    </a>

    <a class="btn btn-success" href="{{ route('admin.tasks.edit', $task->id) }}">
        {{ trans('global.edit') }}
    </a>
</div>

<div class="card">
    <div class="card-header">
        Taak 
    </div>

    <div class="card-body">
        <div class="form-group">
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
        </div>
    </div>
</div>



@endsection