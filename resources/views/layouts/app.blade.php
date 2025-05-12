<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title> Task Managmen – Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  
  <!-- Bootstrap CSS & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
      --primary-color: #6366f1;
      --sidebar-bg: #f8f9fa;
      --sidebar-text: #1f2937;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f1f5f9;
    }

    .navbar {
      border-bottom: 1px solid #e5e7eb;
    }

    .sidebar {
      background: var(--sidebar-bg);
      border-right: 1px solid #e5e7eb;
      width: 280px;
      transition: all 0.3s;
    }

    .sidebar .list-group-item {
      border-radius: 8px;
      margin: 4px 12px;
      padding: 12px 16px;
      color: var(--sidebar-text);
      border: none;
      transition: all 0.2s;
    }

    .sidebar .list-group-item:hover {
      background-color: #eef2ff;
      color: var(--primary-color);
    }

    .sidebar .list-group-item.active {
      background-color: var(--primary-color);
      color: white;
      font-weight: 500;
    }

    .sidebar .sub-menu {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 8px;
      margin: 0 16px;
    }

    .sidebar .sub-menu a {
      padding: 8px 16px;
      color: #4b5563;
      font-size: 0.9rem;
    }

    .sidebar .sub-menu a:hover {
      color: var(--primary-color);
      background: transparent;
    }

    .main-content {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .user-greeting {
      color: var(--primary-color);
      font-weight: 500;
    }

    .logout-link {
      transition: all 0.2s;
    }

    .logout-link:hover {
      color: #dc2626 !important;
    }
  </style>
</head>
<body class="d-flex flex-column vh-100">

  <!-- Header -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container-fluid">
      <a class="navbar-brand fw-semibold text-primary" href="#">
        <i class="bi bi-columns-gap me-2"></i>Task Management
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 user-greeting">
          <i class="bi bi-person-circle me-2"></i>{{ Auth::user()->name ?? 'User' }}
        </span>
        <a href="#" class="logout-link text-danger d-flex align-items-center"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </div>
    </div>
  </nav>

  <div class="d-flex flex-grow-1 overflow-hidden">
    <!-- Sidebar -->
    <aside class="sidebar d-none d-md-block">
      <div class="list-group list-group-flush pt-3">
        <a href="{{ route('dashboard') }}" 
           class="list-group-item list-group-item-action d-flex align-items-center">
          <i class="bi bi-speedometer2 me-3"></i>Dashboard
        </a>

        <!-- Collapsible Section -->
        <div class="px-3 pt-3 text-uppercase small fw-bold text-muted">Management</div>
        
        <a class="list-group-item list-group-item-action d-flex align-items-center"
           data-bs-toggle="collapse" href="#manageSub" role="button">
          <i class="bi bi-folder2-open me-3"></i>Manage & Record
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        
        <div class="collapse show" id="manageSub">
          <div class="sub-menu py-2">
             @if (in_array(Auth::user()->role, ['admin']))
           
                 <a href="{{ route('users.index') }}" class="d-flex align-items-center text-decoration-none py-2">
              <i class="bi bi-person-fill me-3"></i> Users

            </a>
            @endif
                @if (in_array(Auth::user()->role, ['admin']))
                 <a href="{{ route('categories.index') }}" class="d-flex align-items-center text-decoration-none py-2">
              <i class="bi bi-tags-fill me-3"></i>Categories
            </a>
            @endif
              @if (in_array(Auth::user()->role, ['admin', 'manager']))
              <a href="{{ route('tasks.index') }}" class="d-flex align-items-center text-decoration-none py-2">
              <i class="bi bi-list-task me-3"></i>Tasks
            </a>
             @endif
      
            <a href="{{ route('comments.index') }}" class="d-flex align-items-center text-decoration-none py-2">
              <i class="bi bi-chat-dots-fill me-3"></i>Comments 
             </a> 
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow-1 overflow-auto p-4">
      <div class="main-content p-4">
        @yield('content')
      </div>
    </main>
  </div>

  <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-auto">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <small class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}</small>
      <div class="social-links">
        <a href="#" class="text-secondary me-3"><i class="bi bi-github"></i></a>
        <a href="#" class="text-secondary me-3"><i class="bi bi-twitter"></i></a>
        <a href="#" class="text-secondary"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   @stack('scripts')
</body>
</html>