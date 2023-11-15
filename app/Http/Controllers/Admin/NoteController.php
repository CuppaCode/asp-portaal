<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyNoteRequest;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Claim;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use App\Models\Comment;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class NoteController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('note_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $notes = Note::with(['claims', 'user', 'team'])->get();

        $claims = Claim::get();

        $users = User::get();

        $teams = Team::get();

        return view('admin.notes.index', compact('claims', 'notes', 'teams', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('note_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::pluck('claim_number', 'id');

        $users = User::pluck('email', 'id')->prepend(trans('global.pleaseSelect'), '');
        
        return view('admin.notes.create', compact('claims', 'users'));
    }

    public function store(StoreNoteRequest $request)
    {
        $claim_id = $request->input('claims');

        $note = Note::create($request->all());
        $comment = new Comment(['body' => 'A new comment.', 'user_id' => '1']);

        $note_id = Note::find($note->id);

        $note_id->comments()->save($comment);

        $note->claims()->sync($request->input('claims', []));
        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $note->id]);
        }

        if($request->input('add-new-note', 'true')) {
            return redirect()->route('admin.claims.show', $claim_id[0]);
        } else {
            return redirect()->route('admin.notes.index');
        }
    }

    public function edit(Note $note)
    {
        abort_if(Gate::denies('note_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $claims = Claim::pluck('claim_number', 'id');

        $users = User::pluck('email', 'id')->prepend(trans('global.pleaseSelect'), '');

        $note->load('claims', 'user', 'team');

        return view('admin.notes.edit', compact('claims', 'note', 'users'));
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->all());
        $note->claims()->sync($request->input('claims', []));

        return redirect()->route('admin.notes.index');
    }

    public function show(Note $note)
    {
        abort_if(Gate::denies('note_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $note->load('claims', 'user', 'team');

        return view('admin.notes.show', compact('note'));
    }

    public function destroy(Note $note)
    {
        abort_if(Gate::denies('note_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $note->delete();

        return back();
    }

    public function massDestroy(MassDestroyNoteRequest $request)
    {
        $notes = Note::find(request('ids'));

        foreach ($notes as $note) {
            $note->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('note_create') && Gate::denies('note_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Note();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
