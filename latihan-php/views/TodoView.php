<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist Keren</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa; 
        }
        .card-header-custom {
            background-color: #007bff; 
            color: white;
            border-bottom: 0;
        }
        .todo-list-item {
            cursor: grab; /* Kursor grab untuk item yang bisa di-drag */
            transition: background-color 0.3s ease;
        }
        .todo-list-item:hover {
            background-color: #e9ecef;
        }
        /* Style untuk item yang sedang di-drag */
        .sortable-chosen {
            background-color: #ced4da !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .todo-list-item td {
            vertical-align: middle;
        }
        /* Style untuk garis coret pada teks utama */
        .completed-text {
            text-decoration: line-through;
            color: #6c757d !important; /* text-muted */
        }
    </style>
</head>
<body>
<div class="container p-5">
    <div class="card shadow-lg border-0">
        <div class="card-header card-header-custom rounded-top p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="text-white mb-0">
                    <i class="bi bi-list-task me-2"></i> Aplikasi Todo List Keren
                </h1>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTodo">
                    <i class="bi bi-plus-circle-fill me-1"></i> Tambah Data
                </button>
            </div>
        </div>
        
        <div class="card-body p-4">
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-octagon-fill me-2"></i>
                    <?php 
                        if ($error == 'duplicate_title_create' || $error == 'duplicate_title_update') {
                            echo "Gagal: Judul todo sudah ada. Silakan gunakan judul lain.";
                        } else if ($error == 'empty_title_create' || $error == 'empty_title_update') {
                             echo "Gagal: Judul aktivitas tidak boleh kosong.";
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label text-muted"><i class="bi bi-funnel-fill me-1"></i> Filter Status</label>
                    <select id="filterStatus" class="form-select shadow-sm" onchange="applyFilters()">
                        <option value="all" <?= ($currentFilter === null) ? 'selected' : '' ?>>Semua</option>
                        <option value="0" <?= ($currentFilter === '0') ? 'selected' : '' ?>>Belum Selesai</option>
                        <option value="1" <?= ($currentFilter === '1') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="col-md-9">
                    <form action="index.php" method="GET" class="d-flex h-100 align-items-end">
                        <input type="hidden" name="filter" id="hiddenFilterStatus" value="<?= htmlspecialchars($currentFilter ?? 'all') ?>">
                        <input type="text" name="search" class="form-control form-control-lg me-2 shadow-sm" placeholder="Cari Judul atau Deskripsi Todo..." 
                               value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary btn-lg" title="Reset Pencarian dan Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </form>
                </div>
            </div>
            
            <hr class="my-4" />
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 50%;">Judul</th>
                            <th scope="col" style="width: 15%;">Status</th>
                            <th scope="col" style="width: 15%;">Dibuat</th>
                            <th scope="col" style="width: 15%;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="todoTableBody">
                    <?php if (!empty($todos)): ?>
                        <?php foreach ($todos as $i => $todo): ?>
                        <tr class="todo-list-item" data-id="<?= $todo['id'] ?>">
                            <td><i class="bi bi-grip-vertical text-muted"></i> <?= $i + 1 ?></td>
                            <td>
                                <strong class="<?= ($todo['is_finished']) ? 'completed-text' : 'text-dark' ?>"><?= htmlspecialchars($todo['title']) ?></strong>
                                <?php if (!empty($todo['description'])): ?>
                                    <p class="mb-0 text-sm <?= ($todo['is_finished']) ? 'completed-text' : 'text-secondary' ?> truncate-text" style="font-size: 0.85rem; max-height: 2.5em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        <?= htmlspecialchars($todo['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($todo['is_finished']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning">
                                        <i class="bi bi-hourglass-split me-1"></i> Belum Selesai
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                <?= date('d M Y', strtotime($todo['created_at'])) ?>
                            </td>
                            <td>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-sm btn-info text-white" 
                                        onclick="showModalDetailTodo(
                                            '<?= htmlspecialchars(addslashes($todo['title'])) ?>', 
                                            '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>',
                                            '<?= $todo['is_finished'] ? 'Selesai' : 'Belum Selesai' ?>',
                                            '<?= date('d F Y - H:i', strtotime($todo['created_at'])) ?>',
                                            '<?= date('d F Y - H:i', strtotime($todo['updated_at'])) ?>'
                                        )" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning text-dark"
                                        onclick="showModalEditTodo(
                                            <?= $todo['id'] ?>, 
                                            '<?= htmlspecialchars(addslashes($todo['title'])) ?>', 
                                            <?= $todo['is_finished'] ? '1' : '0' ?>,
                                            '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>'
                                        )" title="Ubah">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-4">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                Belum ada data tersedia atau tidak ditemukan!
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addTodo" tabindex="-1" aria-labelledby="addTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addTodoLabel"><i class="bi bi-journal-plus me-2"></i> Tambah Data Todo Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">Judul Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="inputTitle"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                        <div class="form-text">Gunakan judul yang unik.</div>
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" id="inputDescription" rows="3"
                            placeholder="Jelaskan detail aktivitas di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editTodoLabel"><i class="bi bi-pencil-square me-2"></i> Ubah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">Judul Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" id="inputEditTitle"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                        <div class="form-text">Gunakan judul yang unik.</div>
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" id="inputEditDescription" rows="3"
                            placeholder="Jelaskan detail aktivitas di sini..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select" name="is_finished" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailTodo" tabindex="-1" aria-labelledby="detailTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailTodoLabel"><i class="bi bi-info-circle-fill me-2"></i> Detail Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4 text-primary">Judul</dt>
                    <dd class="col-sm-8" id="detailTitle"></dd>
                    
                    <dt class="col-sm-4 text-primary">Status</dt>
                    <dd class="col-sm-8" id="detailStatus"></dd>
                    
                    <dt class="col-sm-4 text-primary">Deskripsi</dt>
                    <dd class="col-sm-8 text-muted" id="detailDescription" style="white-space: pre-wrap;"></dd>

                    <dt class="col-sm-4 text-secondary">Dibuat Pada</dt>
                    <dd class="col-sm-8" id="detailCreatedAt"></dd>

                    <dt class="col-sm-4 text-secondary">Diperbarui Pada</dt>
                    <dd class="col-sm-8" id="detailUpdatedAt"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTodoLabel"><i class="bi bi-trash-fill me-2"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Kamu akan menghapus todo:</p>
                <p class="lead text-danger" id="deleteTodoActivity"></p>
                <div class="alert alert-danger p-2 small">Tindakan ini tidak dapat dibatalkan.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger"><i class="bi bi-x-circle-fill me-1"></i> Ya, Hapus Permanen</a>
            </div>
        </div>
    </div>
</div>


<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
// ===================================
// Fungsi Modal
// ===================================

function showModalEditTodo(todoId, title, isFinished, description) {
    document.getElementById("inputEditTodoId").value = todoId;
    document.getElementById("inputEditTitle").value = title; 
    document.getElementById("selectEditStatus").value = isFinished;
    document.getElementById("inputEditDescription").value = description; 
    var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
    myModal.show();
}

function showModalDeleteTodo(todoId, title) {
    document.getElementById("deleteTodoActivity").innerText = title;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
    var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    myModal.show();
}

function showModalDetailTodo(title, description, status, createdAt, updatedAt) {
    document.getElementById("detailTitle").innerText = title;
    document.getElementById("detailDescription").innerText = description || "*(Tidak ada deskripsi)*"; 
    document.getElementById("detailStatus").innerText = status;
    document.getElementById("detailCreatedAt").innerText = createdAt;
    document.getElementById("detailUpdatedAt").innerText = updatedAt;
    
    var myModal = new bootstrap.Modal(document.getElementById("detailTodo"));
    myModal.show();
}

// ===================================
// Fitur Filter dan Pencarian
// ===================================

function applyFilters() {
    const filterValue = document.getElementById("filterStatus").value;
    const searchParams = new URLSearchParams(window.location.search);
    
    document.getElementById("hiddenFilterStatus").value = filterValue;
    
    if (filterValue === 'all') {
        searchParams.delete('filter');
    } else {
        searchParams.set('filter', filterValue);
    }
    
    const newQueryString = searchParams.toString();
    window.location.href = `index.php${newQueryString ? '?' + newQueryString : ''}`;
}

// ===================================
// Fitur Sorting (Drag and Drop)
// ===================================

var todoTableBody = document.getElementById('todoTableBody');

var sortable = new Sortable(todoTableBody, {
    animation: 150,
    ghostClass: 'sortable-chosen', 
    handle: '.bi-grip-vertical', /* Menargetkan ikon grip sebagai handle */
    
    onEnd: function (evt) {
        var newOrder = Array.from(evt.from.children).map(item => item.dataset.id);
        
        // Kirim urutan baru ke controller/server via AJAX
        fetch('index.php?page=updateSort', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'todo_ids': newOrder.join(',') 
            })
        })
        .then(response => {
            if (response.ok) {
                console.log('Urutan berhasil disimpan secara permanen.');
            } else {
                console.error('Gagal menyimpan urutan.', response.status);
            }
        })
        .catch(error => {
            console.error('Error jaringan saat menyimpan urutan:', error);
        });
    },
});
</script>
</body>
</html>