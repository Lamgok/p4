<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        body {
            background-color: #ffffff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--gray-900);
        }
        
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }
        
        .main-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid var(--gray-200);
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }
        
        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary) 0%, #6366f1 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        .filter-section {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-control-modern {
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        
        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-select-modern {
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        
        .todo-item {
            background: #ffffff;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            cursor: move;
        }
        
        .todo-item:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        
        .todo-item.sortable-ghost {
            opacity: 0.4;
            background: var(--gray-100);
        }
        
        .todo-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .todo-description {
            color: var(--gray-600);
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        
        .todo-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .status-completed {
            background: var(--primary-light);
            color: var(--primary);
        }
        
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        
        .todo-date {
            color: var(--gray-500);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .btn-detail {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        
        .btn-detail:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }
        
        .btn-edit {
            background: #fef3c7;
            color: #d97706;
        }
        
        .btn-edit:hover {
            background: #fde68a;
            color: #b45309;
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .btn-delete:hover {
            background: #fecaca;
            color: #b91c1c;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
        }
        
        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }
        
        .search-container {
            position: relative;
        }
        
        .search-input {
            padding-left: 2.5rem;
        }
        
        .alert-modern {
            border-radius: 12px;
            border: 1px solid;
            padding: 1rem 1.5rem;
        }
        
        .alert-success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        
        .alert-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
        
        @media (max-width: 768px) {
            .todo-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .action-buttons {
                margin-left: 0;
                width: 100%;
                justify-content: flex-end;
            }
            
            .filter-section .row > div {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4 app-container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-tasks me-3 text-primary"></i>
                            Todo List
                        </h1>
                        <p class="text-muted mb-0">Kelola tugas dan aktivitas Anda dengan mudah</p>
                    </div>
                    <button class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addTodo">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="main-card">
            <div class="card-body p-4">
                <!-- Filter dan Search Section -->
                <div class="filter-section">
                    <form method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="page" value="index">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Filter Status</label>
                            <select name="filter" class="form-select form-select-modern" onchange="this.form.submit()">
                                <option value="all" <?= ($_GET['filter'] ?? 'all') === 'all' ? 'selected' : '' ?>>
                                    <i class="fas fa-list me-2"></i>Semua Todo
                                </option>
                                <option value="finished" <?= ($_GET['filter'] ?? '') === 'finished' ? 'selected' : '' ?>>
                                    <i class="fas fa-check-circle me-2"></i>Selesai
                                </option>
                                <option value="unfinished" <?= ($_GET['filter'] ?? '') === 'unfinished' ? 'selected' : '' ?>>
                                    <i class="fas fa-clock me-2"></i>Belum Selesai
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cari Todo</label>
                            <div class="search-container">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" class="form-control form-control-modern search-input" 
                                       placeholder="Cari todo berdasarkan judul atau deskripsi..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary-modern w-100">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                        </div>
                        <?php if (isset($_GET['search']) || (isset($_GET['filter']) && $_GET['filter'] !== 'all')): ?>
                            <div class="col-12">
                                <a href="index.php" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times me-1"></i>Reset Filter
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert-modern alert-danger mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div class="flex-grow-1"><?= $_SESSION['error'] ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert-modern alert-success mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div class="flex-grow-1"><?= $_SESSION['success'] ?></div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Todo List -->
                <div id="todoList">
                    <?php if (!empty($todos)): ?>
                        <?php foreach ($todos as $i => $todo): ?>
                        <div class="todo-item" data-id="<?= $todo['id'] ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="todo-title">
                                        <i class="fas fa-check-circle <?= $todo['is_finished'] === 't' ? 'text-success' : 'text-gray-400' ?>"></i>
                                        <?= htmlspecialchars($todo['title']) ?>
                                    </div>
                                    <div class="todo-description">
                                        <?= htmlspecialchars($todo['description']) ?>
                                    </div>
                                    <div class="todo-meta">
                                        <span class="status-badge <?= $todo['is_finished'] === 't' ? 'status-completed' : 'status-pending' ?>">
                                            <i class="fas <?= $todo['is_finished'] === 't' ? 'fa-check' : 'fa-clock' ?>"></i>
                                            <?= $todo['is_finished'] === 't' ? 'Selesai' : 'Belum Selesai' ?>
                                        </span>
                                        <span class="todo-date">
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d F Y - H:i', strtotime($todo['created_at'])) ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <a href="?page=detail&id=<?= $todo['id'] ?>" class="btn-action btn-detail">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                    <button class="btn-action btn-edit" 
                                            onclick="showModalEditTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>', '<?= htmlspecialchars(addslashes($todo['description'])) ?>', <?= $todo['is_finished'] === 't' ? 'true' : 'false' ?>)">
                                        <i class="fas fa-edit me-1"></i>Ubah
                                    </button>
                                    <button class="btn-action btn-delete" 
                                            onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')">
                                        <i class="fas fa-trash me-1"></i>Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h4 class="text-muted">Belum ada data tersedia!</h4>
                            <p class="text-muted mb-4">Mulai dengan menambahkan todo pertama Anda.</p>
                            <button class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addTodo">
                                <i class="fas fa-plus me-2"></i>Tambah Todo Pertama
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ADD TODO -->
    <div class="modal fade" id="addTodo" tabindex="-1" aria-labelledby="addTodoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="addTodoLabel">
                        <i class="fas fa-plus-circle me-2 text-primary"></i>
                        Tambah Todo Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="?page=create" method="POST">
                    <div class="modal-body pt-0">
                        <div class="mb-3">
                            <label for="inputTitle" class="form-label fw-semibold">Judul *</label>
                            <input type="text" name="title" class="form-control form-control-modern" id="inputTitle" 
                                   placeholder="Masukkan judul todo" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputDescription" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-modern" id="inputDescription" 
                                      rows="3" placeholder="Tambahkan deskripsi (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT TODO -->
    <div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editTodoLabel">
                        <i class="fas fa-edit me-2 text-warning"></i>
                        Edit Todo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="?page=update" method="POST">
                    <input name="id" type="hidden" id="inputEditTodoId">
                    <div class="modal-body pt-0">
                        <div class="mb-3">
                            <label for="inputEditTitle" class="form-label fw-semibold">Judul *</label>
                            <input type="text" name="title" class="form-control form-control-modern" id="inputEditTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputEditDescription" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control form-control-modern" id="inputEditDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_finished" id="inputEditIsFinished" style="width: 1.2em; height: 1.2em;">
                                <label class="form-check-label fw-semibold" for="inputEditIsFinished">
                                    Tandai sebagai selesai
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE TODO -->
    <div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger" id="deleteTodoLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Hapus Todo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="text-center mb-3">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <p class="text-center">
                        Anda akan menghapus todo: <br>
                        <strong class="text-danger" id="deleteTodoTitle"></strong>
                    </p>
                    <p class="text-muted text-center small">
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <a id="btnDeleteTodo" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Ya, Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        // Modal Functions
        function showModalEditTodo(todoId, title, description, isFinished) {
            document.getElementById("inputEditTodoId").value = todoId;
            document.getElementById("inputEditTitle").value = title;
            document.getElementById("inputEditDescription").value = description;
            document.getElementById("inputEditIsFinished").checked = isFinished;
            var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
            myModal.show();
        }

        function showModalDeleteTodo(todoId, title) {
            document.getElementById("deleteTodoTitle").innerText = title;
            document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
            var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
            myModal.show();
        }

        // Drag and Drop Sorting
        document.addEventListener('DOMContentLoaded', function() {
            const todoList = document.getElementById('todoList');
            
            if (todoList) {
                new Sortable(todoList, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const todoItems = Array.from(todoList.querySelectorAll('.todo-item'));
                        const todoIds = todoItems.map(item => item.getAttribute('data-id'));
                        
                        // Simpan urutan ke server
                        fetch('?page=updateSort', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ todos: todoIds })
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>