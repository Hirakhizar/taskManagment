@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2>Manage Users</h2>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <div class="text-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddModal()">
            <i class="bi bi-plus-circle me-1"></i> Add User
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td class="text-end">
                                <button
                                  class="btn btn-sm btn-outline-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#userModal"
                                  onclick="openEditModal(
                                    '{{ $user->id }}',
                                    '{{ addslashes($user->name) }}',
                                    '{{ $user->email }}',
                                    '{{ $user->role }}'
                                  )"
                                >
                                  <i class="bi bi-pencil-square me-1"></i>
                                </button>

                                <form
                                  action="{{ route('users.destroy', $user) }}"
                                  method="POST"
                                  class="d-inline"
                                  id="deleteFormUser{{ $user->id }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                      type="button"
                                      class="btn btn-sm btn-outline-danger delete-btn"
                                      data-id="{{ $user->id }}"
                                    >
                                      <i class="bi bi-trash me-1"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="userForm" method="POST">
      @csrf
      <input type="hidden" name="_method" id="userFormMethod" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Name -->
          <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" id="userName" name="name" class="form-control" required>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="userEmail" class="form-label">Email</label>
            <input type="email" id="userEmail" name="email" class="form-control" required>
          </div>

          <!-- Password fields wrapper -->
          <div id="passwordFields">
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
          </div>

          <!-- Role -->
          <div class="mb-3">
            <label for="userRole" class="form-label">Role</label>
            <select name="role" id="userRole" class="form-select" required>
              <option value="user">User</option>
              <option value="manager">Manager</option>
              <option value="admin">Admin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="userSubmitBtn">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function openAddModal() {
    document.getElementById('userModalLabel').innerText = 'Add User';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userFormMethod').value = 'POST';

    // reset inputs
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userRole').value = 'user';

    // show password fields & make required
    const pwdGroup = document.getElementById('passwordFields');
    pwdGroup.style.display = 'block';
    pwdGroup.querySelectorAll('input').forEach(i => i.required = true);
  }

  function openEditModal(id, name, email, role) {
    const url = '{{ route("users.update", ":id") }}'.replace(':id', id);
    document.getElementById('userModalLabel').innerText = 'Edit User';
    document.getElementById('userForm').action = url;
    document.getElementById('userFormMethod').value = 'PUT';

    // fill inputs
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userRole').value = role;

    // hide password fields & remove required
    const pwdGroup = document.getElementById('passwordFields');
    pwdGroup.style.display = 'none';
    pwdGroup.querySelectorAll('input').forEach(i => i.required = false);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        Swal.fire({
          title: 'Are you sure?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete!',
        }).then(res => {
          if (res.isConfirmed) {
            document.getElementById('deleteFormUser' + id).submit();
          }
        });
      });
    });
  });
</script>
@endpush
