<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

         public function index()
    {
        
        $comments = Comment::get();
        return view('comments.index', compact('comments'));
    }

 public function latest()
{
    $comments = Comment::with(['task', 'user'])
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($comment) {
            return [
                'comment' =>$comment->comment,
                'task_title' => $comment->task->title ?? '—',
                'created_at' => $comment->created_at->diffForHumans(),
                'user_name' => $comment->user->name
            ];
        });

    return response()->json($comments);
}

   public function store(Request $request)
{
    $request->validate([
        'task_id' => 'required|exists:tasks,id',
        'comment' => 'required|string|max:1000',
    ]);

    $comment = Comment::create([
        'task_id' => $request->task_id,
        'user_id' => Auth::id(),
        'comment' => $request->comment,
    ]);


        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->comment,
            'user' => [
                'name' => Auth::user()->name
            ]
        ]);
  

   

}
}