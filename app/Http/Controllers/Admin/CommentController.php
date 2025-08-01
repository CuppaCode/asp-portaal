<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Note;
use App\Models\Task;
use App\Models\Claim;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comments = Comment::get();

        return view('admin.comments.index', compact('comments'));
    }

    public function create()
    {
        abort_if(Gate::denies('comment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.comments.create');
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = Comment::create($request->all());

        return redirect()->route('admin.comments.index');
    }

    public function edit(Comment $comment)
    {
        abort_if(Gate::denies('comment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.comments.edit', compact('comment'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update($request->all());

        return redirect()->route('admin.comments.index');
    }

    public function show(Comment $comment)
    {
        abort_if(Gate::denies('comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // $comment->load('note');

        return view('admin.comments.show', compact('comment'));
    }

    public function destroy(Request $request, Comment $comment)
    {
        // Check if the user has permission to delete the comment
        if (Gate::denies('comment_delete')) {
            return response()->json([
                'type' => 'alert-danger',
                'message' => 'Je hebt geen toestemming om deze opmerking te verwijderen.'
            ], Response::HTTP_FORBIDDEN);
        }

        try {
            $comment->delete();

            return response()->json([
                'type' => 'alert-success',
                'message' => 'De opmerking is succesvol verwijderd.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'alert-danger',
                'message' => 'Er is een fout opgetreden bij het verwijderen van de opmerking.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function massDestroy(MassDestroyCommentRequest $request)
    {
        $comments = Comment::find(request('ids'));

        foreach ($comments as $comment) {
            $comment->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function quickStore(Request $request)
    {
        
        $comment = Comment::create([
            'body' => $request->body,
            'commentable_id' => $request->commentableID,
            'commentable_type' => $request->commentableType,
            'user_id' => $request->userID,
            'team_id' => $request->teamID
        ]);

        $allComments = $comment->commentable->comments;

        if($comment->commentable_type === 'App\Models\Task') {
            $task = Task::where('id', $comment->commentable_id)->first();
            $claim = Claim::where('id', $task->claim_id)->first();
            $user = User::where('id', $task->created_by)->first();
            $body = $comment->body;

            $message = new \App\Notifications\TaskCommentUpdate($task, $claim, $user, $body);

            Notification::route('mail', [
                $user->email => $user->name])->notify($message);
        }

        return response()->json([
            'type'  => 'alert-success',
            'allComments' => $allComments,
            'message' => 'Comment successvol geplaatst'
        ]);

        
    }
}
