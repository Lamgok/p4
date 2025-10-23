<!DOCTYPE html>
<html>
<head>
    <title>PHP - Aplikasi Todolist</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .todo-list-item {
            cursor: move; /* Menandakan elemen dapat dipindahkan */
        }
        .todo-list-item.sortable-chosen {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
<div class="container-fluid p-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">📝 Todo List Sederhana</h1>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTodo">
                    <i class="bi bi-plus-circle"></i> Tambah Data
                </button>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php 
                        if ($error == 'duplicate_title_create' || $error == 'duplicate_title_update') {
                            echo "❌ Gagal: Judul todo sudah ada. Silakan gunakan judul lain.";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filterStatus" class="form-label">Filter Status</label>
                    <select id="filterStatus" class="form-select" onchange="applyFilters()">
                        <option value="all" <?= ($currentFilter === null) ? 'selected' : '' ?>>Semua</option>
                        <option value="0" <?= ($currentFilter === '0') ? 'selected' : '' ?>>Belum Selesai</option>
                        <option value="1" <?= ($currentFilter === '1') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <form action="index.php" method="GET" class="d-flex">
                        <input type="hidden" name="filter" id="hiddenFilterStatus" value="<?= htmlspecialchars($currentFilter ?? 'all') ?>">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari Judul Todo..." 
                               value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                        <button type="submit" class="btn btn-info text-white">Cari</button>
                        <a href="index.php" class="btn btn-secondary ms-2">Reset</a>
                    </form>
                </div>
            </div>
            
            <hr />
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody id="todoTableBody">
                    <?php if (!empty($todos)): ?>
                        <?php foreach ($todos as $i => $todo): ?>
                        <tr class="todo-list-item" data-id="<?= $todo['id'] ?>">
                            <td><?= $i + 1 ?></td>
                            <td class="<?= ($todo['status']) ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= htmlspecialchars($todo['activity']) ?>
                            </td>
                            <td>
                                <?php if ($todo['status']): ?>
                                    <span class="badge bg-success">Selesai ✅</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Belum Selesai ⏳</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d F Y - H:i', strtotime($todo['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info text-white" 
                                    onclick="showModalDetailTodo(
                                        '<?= htmlspecialchars(addslashes($todo['activity'])) ?>', 
                                        '<?= $todo['status'] ? 'Selesai' : 'Belum Selesai' ?>',
                                        '<?= date('d F Y - H:i', strtotime($todo['created_at'])) ?>',
                                        '<?= date('d F Y - H:i', strtotime($todo['updated_at'])) ?>'
                                    )">
                                    Detail
                                </button>
                                <button class="btn btn-sm btn-warning"
                                    onclick="showModalEditTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['activity'])) ?>', <?= $todo['status'] ?>)">
                                    Ubah
                                </button>
                                <button class="btn btn-sm btn-danger"
                                    onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['activity'])) ?>')">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data tersedia atau tidak ditemukan!</td>
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
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addTodoLabel">Tambah Data Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputActivity" class="form-label">Judul Aktivitas</label>
                        <input type="text" name="activity" class="form-control" id="inputActivity"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                        <div class="form-text">Gunakan judul yang unik.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editTodo" tabindex="-1" aria-labelledby="editTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editTodoLabel">Ubah Data Todo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?page=update" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditActivity" class="form-label">Judul Aktivitas</label>
                        <input type="text" name="activity" class="form-control" id="inputEditActivity"
                            placeholder="Contoh: Belajar membuat aplikasi website sederhana" required>
                        <div class="form-text">Gunakan judul yang unik.</div>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">Status</label>
                        <select class="form-select" name="status" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteTodo" tabindex="-1" aria-labelledby="deleteTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteTodoLabel">Hapus Data Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Kamu akan menghapus todo <strong class="text-danger" id="deleteTodoActivity"></strong>. Apakah kamu yakin?</p>
                <div class="alert alert-danger">Tindakan ini tidak dapat dibatalkan.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a id="btnDeleteTodo" class="btn btn-danger">Ya, Hapus Permanen</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailTodo" tabindex="-1" aria-labelledby="detailTodoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailTodoLabel">Detail Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Judul</dt>
                    <dd class="col-sm-8" id="detailTitle"></dd>
                    
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8" id="detailStatus"></dd>
                    
                    <dt class="col-sm-4">Deskripsi</dt>
                    <dd class="col-sm-8 text-muted">*(Fitur Deskripsi tidak tersedia tanpa perubahan database)*</dd>

                    <dt class="col-sm-4">Dibuat Pada</dt>
                    <dd class="col-sm-8" id="detailCreatedAt"></dd>

                    <dt class="col-sm-4">Diperbarui Pada</dt>
                    <dd class="col-sm-8" id="detailUpdatedAt"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

function showModalEditTodo(todoId, activity, status) {
    document.getElementById("inputEditTodoId").value = todoId;
    document.getElementById("inputEditActivity").value = activity;
    document.getElementById("selectEditStatus").value = status;
    var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
    myModal.show();
}

function showModalDeleteTodo(todoId, activity) {
    document.getElementById("deleteTodoActivity").innerText = activity;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
    var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    myModal.show();
}

// FUNGSI INI UNTUK MENAMPILKAN MODAL DETAIL
function showModalDetailTodo(title, status, createdAt, updatedAt) {
    document.getElementById("detailTitle").innerText = title;
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
    
    // Set filter di input hidden untuk form pencarian
    document.getElementById("hiddenFilterStatus").value = filterValue;
    
    // Lakukan redirect ke index.php dengan parameter filter
    if (filterValue === 'all') {
        searchParams.delete('filter');
    } else {
        searchParams.set('filter', filterValue);
    }
    
    // Pertahankan parameter search jika ada
    const newQueryString = searchParams.toString();
    window.location.href = `index.php${newQueryString ? '?' + newQueryString : ''}`;
}

// ===================================
// Fitur Sorting (Drag and Drop)
// ===================================

// Inisialisasi SortableJS
var todoTableBody = document.getElementById('todoTableBody');

var sortable = new Sortable(todoTableBody, {
    animation: 150,
    ghostClass: 'sortable-ghost',
    handle: '.todo-list-item',
    // Urutan baru tidak disimpan permanen karena batasan DB.
    onEnd: function (evt) {
        var newOrder = Array.from(evt.from.children).map(item => item.dataset.id);
        console.log('Urutan baru (tidak disimpan permanen karena batasan DB):', newOrder);
    },
});
</script>
</body>
</html>