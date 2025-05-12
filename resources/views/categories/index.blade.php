@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Manage Categories</h2>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <!-- Add Button -->
     
    <div class="text-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="openAddModal()">
            Add Category
        </button>
    </div>

    <!-- Category Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td class="text-end">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#categoryModal"
                                        onclick="openEditModal('{{ $category->id }}', '{{ $category->name }}')">
                                   <i class="bi bi-pencil-square me-1"></i> 
                                </button>

                                <!-- Delete Form -->
                               <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" id="deleteForm{{ $category->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $category->id }}">
                                        <i class="bi bi-trash me-1"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" id="categoryName" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Script -->


<script>

    function openAddModal() {
        document.getElementById('categoryModalLabel').innerText = 'Add Category';
        document.getElementById('categoryForm').action = "{{ route('categories.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('categoryName').value = '';
    }

    function openEditModal(id, name) {
        const url = '{{ route("categories.update", ":id") }}'.replace(':id', id);
        document.getElementById('categoryModalLabel').innerText = 'Edit Category';
        document.getElementById('categoryForm').action = url;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('categoryName').value = name;
    }
    document.addEventListener('DOMContentLoaded', function () {
    // Select all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const categoryId = this.getAttribute('data-id');
            const form = document.getElementById('deleteForm' + categoryId);
            
            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    form.submit();
                }
            });
        });
    });
});

</script>
@endsection
