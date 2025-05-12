@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-semibold">Overview</h2>
            </div>
        </div>

        <div class="row g-4">
            {{-- Tasks --}}
            <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-list-task me-1"></i> Recent Tasks
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($tasks as $task)
                            <li class="list-group-item">
                                <strong>{{ $task->title }}</strong><br>
                                <small class="text-muted">{{ $task->created_at->format('M j, Y') }}</small>

                                {{-- Task Comments --}}
                                @if ($task->comments->count())
                                    <ul class="mt-2 ps-3 small text-muted">
                                        @foreach ($task->comments as $comment)
                                            <li>{{ $comment->comment }} <em>— {{ $comment->user->name }}</em></li>
                                        @endforeach
                                    </ul>
                                @endif

                                {{-- Add Comment Form --}}

                                <form action="{{ route('comments.store') }}" method="POST" class="mt-2 comment-form"
                                    data-task-id="{{ $task->id }}">
                                    @csrf
                                    <input type="hidden" name="task_id" value="{{ $task->id }}">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="comment" class="form-control"
                                            placeholder="Add comment..." required>
                                        <button class="btn btn-primary" type="submit">Post</button>
                                    </div>
                                </form>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">No tasks found.</li>
                        @endforelse
                    </ul>
                    <div class="card-footer text-center">
                      
                        <a href="{{ route('tasks.index') }}" class="small">View all tasks</a>
                    </div>
                </div>
            </div>


            {{-- <div class="col-md-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-chat-dots-fill me-1"></i> Latest Comments
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($comments as $comment)
                            <li class="list-group-item">
                                <p class="mb-1">{{ Str::limit($comment->comment, 50) }}</p>
                                <small class="text-muted">
                                    on <em>{{ $comment->task->title ?? '—' }}</em>,
                                    {{ $comment->created_at->diffForHumans() }}
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">No comments yet.</li>
                        @endforelse
                    </ul>
                    <div class="card-footer text-center">

                        <a href=" {{ route('comments.index') }}" class="small">View all comments</a>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-4">
    <div class="card shadow-sm h-100">
        <div class="card-header bg-warning text-dark">
            <i class="bi bi-chat-dots-fill me-1"></i> Latest Comments
            <button id="refresh-comments" class="btn btn-sm btn-light float-end">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
        <ul class="list-group list-group-flush" id="latest-comments-list">
            <li class="list-group-item text-center text-muted">Loading comments...</li>
        </ul>
        <div class="card-footer text-center">
            <a href="{{ route('comments.index') }}" class="small">View all comments</a>
        </div>
    </div>
</div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
   
    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const taskId = form.dataset.taskId;
            const commentList = form.closest('li').querySelector('ul');
            const commentInput = form.querySelector('input[name="comment"]');

            try {
                const response = await fetch(`{{ route('comments.store') }}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Add new comment to list
                    const newComment = document.createElement('li');
                    newComment.innerHTML = `
                        ${data.comment} 
                        <em>— ${data.user.name}</em>
                        <small class="text-muted">(just now)</small>
                    `;
                    
                    if (!commentList) {
                        const newList = document.createElement('ul');
                        newList.classList.add('mt-2', 'ps-3', 'small', 'text-muted');
                        newList.appendChild(newComment);
                        form.closest('li').querySelector('strong').after(newList);
                    } else {
                        commentList.prepend(newComment);
                    }

                    // Clear input
                    commentInput.value = '';
                } else {
                    alert(data.message || 'Error submitting comment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentsList = document.getElementById('latest-comments-list');
    const refreshButton = document.getElementById('refresh-comments');

    function loadComments() {
        commentsList.innerHTML = '<li class="list-group-item text-center text-muted">Loading comments...</li>';
        
        fetch('{{ route('comments.latest') }}')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(comments => {
                commentsList.innerHTML = '';
                
                if (comments.length === 0) {
                    commentsList.innerHTML = '<li class="list-group-item text-center text-muted">No comments yet.</li>';
                    return;
                }

                comments.forEach(comment => {
                    const li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `
                        <p class="mb-1">${comment.comment}</p>
                        <small class="text-muted">
                            on <em>${comment.task_title}</em>,
                            ${comment.created_at}
                        </small>
                    `;
                    commentsList.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                commentsList.innerHTML = '<li class="list-group-item text-center text-danger">Error loading comments</li>';
            });
    }

    
    loadComments();


    refreshButton.addEventListener('click', loadComments);

   
    setInterval(loadComments, 30000);
});
</script>
@endpush