@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">All Comments</h2>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($comments as $comment)
                    <li class="list-group-item">
                        <p class="mb-1">{{ $comment->comment }}</p>
                        <small class="text-muted">
                            on task: <strong>{{ $comment->task->title ?? 'N/A' }}</strong> by 
                            <em>{{ $comment->user->name ?? 'Unknown' }}</em> 
                            • {{ $comment->created_at->diffForHumans() }}
                        </small>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">No comments yet.</li>
                @endforelse
            </ul>
        </div>
       
    </div>
</div>
@endsection
