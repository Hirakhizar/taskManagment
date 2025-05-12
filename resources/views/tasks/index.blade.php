@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Task Management</h2>
            <!-- Create Task Button -->
            @if (in_array(Auth::user()->role, ['admin', 'manager']))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-circle me-1"></i> Create Task
                </button>
            @endif
        </div>
        <input type="text" id="searchInput" class="form-control mb-3"
            placeholder="Search tasks by title, priority, status...">

        <!-- Task Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>File</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Assigned To</th>
                                @if (in_array(Auth::user()->role, ['admin', 'manager']))
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->category->name }}</td>
                                    @php
                                        $ext = strtolower(pathinfo($task->file ?? '', PATHINFO_EXTENSION));
                                    @endphp

                                    <td>
                                        @if ($task->file)
                                            @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('storage/' . $task->file) }}" style="max-height:80px;"
                                                    alt="">
                                            @elseif($ext === 'pdf')
                                                <iframe src="{{ asset('storage/' . $task->file) }}" width="200"
                                                    height="120"></iframe>
                                            @else
                                                <a href="{{ asset('storage/' . $task->file) }}" target="_blank">
                                                    {{ \Illuminate\Support\Str::afterLast($task->file, '/') }}
                                                </a>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>

                                    <td>{{ ucfirst($task->priority) }}</td>
                                    <td>{{ ucfirst($task->status) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($task->end_date)->format('d-m-Y') }}</td>
                                    <td>{{ $task->assignedTo->name }}</td>
                                    @if (in_array(Auth::user()->role, ['admin', 'manager']))
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editTaskModal{{ $task->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                                class="d-inline" id="deleteForm{{ $task->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-id="{{ $task->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>

                                <!-- Edit Task Modal -->
                                <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1"
                                    aria-labelledby="editTaskModalLabel{{ $task->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('tasks.update', $task->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Task</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label>Title</label>
                                                        <input type="text" name="title" class="form-control"
                                                            value="{{ $task->title }}" required>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Description</label>
                                                        <textarea name="description" class="form-control">{{ $task->description }}</textarea>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Category</label>
                                                        <select name="category_id" class="form-select" required>
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}"
                                                                    {{ $task->category_id == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label>Priority</label>
                                                        <select name="priority" class="form-select">
                                                            <option value="low"
                                                                {{ $task->priority == 'low' ? 'selected' : '' }}>Low
                                                            </option>
                                                            <option value="medium"
                                                                {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium
                                                            </option>
                                                            <option value="high"
                                                                {{ $task->priority == 'high' ? 'selected' : '' }}>High
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending"
                                                                {{ $task->status == 'pending' ? 'selected' : '' }}>Pending
                                                            </option>
                                                            <option value="in_progress"
                                                                {{ $task->status == 'in_progress' ? 'selected' : '' }}>In
                                                                Progress</option>
                                                            <option value="completed"
                                                                {{ $task->status == 'completed' ? 'selected' : '' }}>
                                                                Completed</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>Start Date</label>
                                                        <input type="date" name="start_date" class="form-control"
                                                            value="{{ $task->start_date }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>End Date</label>
                                                        <input type="date" name="end_date" class="form-control"
                                                            value="{{ $task->end_date }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label>File</label>
                                                        <input type="file" name="file" class="form-control">
                                                        @if ($task->file)
                                                            <small>Current: <a href="{{ asset('storage/' . $task->file) }}"
                                                                    target="_blank">Download</a></small>
                                                        @endif
                                                    </div>
                                                   @php
                                                    $loggedInUser = auth()->user();
                                                @endphp

                                                <div class="mb-2">
                                                    <label>Assign To</label>
                                                    <select name="assigned_to" class="form-select">
                                                        @foreach (\App\Models\User::all() as $user)
                                                            {{-- if the current user is a manager, skip any admin users --}}
                                                            @if (!($loggedInUser->role === 'manager' && $user->role === 'admin'))
                                                                <option value="{{ $user->id }}"
                                                                    {{ $task->assigned_to == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>



                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Update Task</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="mb-2">
                            <label>Priority</label>
                            <select name="priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" selected>Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>File</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label>Assign To</label>
                            <select name="assigned_to" class="form-select">
                                @foreach (\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </div>
            </form>
        </div>


    </div>
@endsection

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <script>
        // 🔍 Live Search Script
        document.getElementById('searchInput').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(function(row) {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });

        document.querySelectorAll('.delete-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                const taskId = this.getAttribute('data-id');
                const deleteForm = document.getElementById('deleteForm' + taskId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
